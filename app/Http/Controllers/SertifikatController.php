<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\PendaftaranEvent;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SertifikatController extends Controller
{
    /**
     * Penyelenggara: Upload template sertifikat & konfigurasi.
     */
    public function uploadTemplate(Request $request, $event_id)
    {
        $request->validate([
            'template' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'config' => 'required|json'
        ]);

        $event = Event::find($event_id);

        if (!$event) {
            return response()->json(['message' => 'Event tidak ditemukan'], 404);
        }

        // Pastikan hanya penyelenggara event tersebut yang bisa upload
        if ($event->user_id !== $request->user()->id_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses'], 403);
        }

        if ($request->hasFile('template')) {
            $event->sertifikat_template = Cloudinary::upload($request->file('template')->getRealPath())->getSecurePath();
        }

        $event->sertifikat_config = json_decode($request->config, true);
        $event->save();

        return response()->json([
            'message' => 'Template dan konfigurasi sertifikat berhasil disimpan',
            'data' => [
                'sertifikat_template' => $event->sertifikat_template ? url($event->sertifikat_template) : null,
                'sertifikat_config' => $event->sertifikat_config
            ]
        ], 200);
    }

    /**
     * Penyelenggara: Hapus template sertifikat.
     */
    public function deleteTemplate(Request $request, $event_id)
    {
        $event = Event::find($event_id);

        if (!$event) {
            return response()->json(['message' => 'Event tidak ditemukan'], 404);
        }

        if ($event->user_id !== $request->user()->id_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses'], 403);
        }

        if ($event->sertifikat_template) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $event->sertifikat_template));
            $event->sertifikat_template = null;
            $event->save();
        }

        return response()->json(['message' => 'Template berhasil dihapus'], 200);
    }

    /**
     * Penyelenggara: Generate sertifikat untuk semua peserta event yang memenuhi syarat.
     */
    public function generateForEvent(Request $request, $event_id)
    {
        $event = Event::find($event_id);

        if (!$event) {
            return response()->json(['message' => 'Event tidak ditemukan'], 404);
        }

        if ($event->user_id !== $request->user()->id_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses'], 403);
        }

        if (!$event->sertifikat_template) {
            return response()->json(['message' => 'Template sertifikat belum diupload'], 422);
        }

        // Cek apakah event sudah selesai (tanggal < now())
        if (strtotime($event->tanggal) > time()) {
            return response()->json(['message' => 'Event belum selesai, sertifikat belum bisa digenerate'], 403);
        }

        // Ambil pendaftaran yang memenuhi syarat:
        // 1. Hadir
        // 2. Pembayaran terverifikasi ATAU tiket gratis
        // 3. Belum punya sertifikat
        $pendaftarans = PendaftaranEvent::with(['user', 'pembayaran'])
            ->where('event_id', $event_id)
            ->where('status_pendaftaran', 'hadir')
            ->whereNull('sertifikat_url')
            ->get();

        $berhasil = 0;
        $gagal = 0;

        // Konversi ke base64 agar mudah di-load di DOMPDF
        try {
            $data = \Illuminate\Support\Facades\Http::get($event->sertifikat_template)->body();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengunduh template dari server gambar'], 500);
        }
        $base64Template = 'data:image/png;base64,' . base64_encode($data);

        $config = $event->sertifikat_config ?: [
            'x' => '50%',
            'y' => '50%',
            'fontSize' => '30px',
            'color' => '#000000',
            'align' => 'center'
        ];

        foreach ($pendaftarans as $pendaftaran) {
            // Cek pembayaran jika event berbayar
            $isGratis = $pendaftaran->total_harga == 0;
            $pembayaranTerverifikasi = $pendaftaran->pembayaran->where('status_pembayaran', 'terverifikasi')->isNotEmpty();

            if (!$isGratis && !$pembayaranTerverifikasi) {
                // Skip jika berbayar tapi belum lunas
                continue;
            }

            try {
                $nama_peserta = optional($pendaftaran->user)->nama_lengkap ?? 'Peserta';
                
                // Siapkan data untuk view
                $dataView = [
                    'bgImage' => $base64Template,
                    'namaPeserta' => $nama_peserta,
                    'config' => $config
                ];

                // Render PDF
                $pdf = Pdf::loadView('sertifikat.template', $dataView)->setPaper('a4', 'landscape');
                
                // Simpan ke storage
                $fileName = 'sertifikat_' . Str::slug($event->nama_event) . '_' . Str::slug($nama_peserta) . '_' . uniqid() . '.pdf';
                $filePath = 'sertifikat/generated/' . $fileName;
                
                Storage::disk('public')->put($filePath, $pdf->output());

                // Update database
                $pendaftaran->sertifikat_url = '/storage/' . $filePath;
                $pendaftaran->save();

                $berhasil++;
            } catch (\Exception $e) {
                \Log::error('Cert Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                $gagal++;
                $lastError = $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Proses generate sertifikat selesai',
            'summary' => [
                'total_diproses' => $berhasil + $gagal,
                'berhasil' => $berhasil,
                'gagal' => $gagal,
                'last_error' => $lastError ?? null
            ]
        ], 200);
    }

    /**
     * User: Lihat daftar sertifikat saya.
     */
    public function indexUser(Request $request)
    {
        $user_id = $request->user()->id_user;

        $sertifikats = PendaftaranEvent::with('event')
            ->where('user_id', $user_id)
            ->whereNotNull('sertifikat_url')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($pendaftaran) {
                return [
                    'id' => $pendaftaran->id,
                    'event_name' => optional($pendaftaran->event)->nama_event,
                    'event_date' => optional($pendaftaran->event)->tanggal,
                    'sertifikat_url' => url($pendaftaran->sertifikat_url),
                    'generated_at' => $pendaftaran->updated_at
                ];
            });

        return response()->json([
            'message' => 'Berhasil mengambil daftar sertifikat',
            'data' => $sertifikats
        ], 200);
    }
}
