<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    private function transformPembayaran($pembayaran)
    {
        $pendaftaran = $pembayaran->pendaftaran;
        $user = optional($pendaftaran)->user;
        $event = optional($pendaftaran)->event;

        $pembayaran->nama_peserta = optional($user)->nama_lengkap;
        $pembayaran->email_peserta = optional($user)->email;
        $pembayaran->nama_event = optional($event)->nama_event;
        $pembayaran->nama_metode = $pembayaran->metode_pembayaran_custom ?: optional($pembayaran->metodePembayaran)->nama_metode;
        
        // Tambahkan data dari pendaftaran
        $pembayaran->custom_form_responses = optional($pendaftaran)->custom_form_responses;
        $pembayaran->jumlah_tiket = optional($pendaftaran)->jumlah_tiket;
        $pembayaran->total_harga_pendaftaran = optional($pendaftaran)->total_harga;
        $pembayaran->tanggal_daftar = optional($pendaftaran)->tanggal_daftar;
        $pembayaran->status_pendaftaran = optional($pendaftaran)->status_pendaftaran;

        // Bukti pembayaran URL sekarang dihandle otomatis oleh model (accessor)

        return $pembayaran;
    }

    public function verifikasi(Request $request, $id)
    {
        $user = $request->user();
        $pembayaran = Pembayaran::with('pendaftaran.event')->find($id);

        if (!$pembayaran) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        // Security check: only organizer of the event can verify
        if ($user->role === 'penyelenggara' && $pembayaran->pendaftaran->event->user_id !== $user->id_user) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk memverifikasi pembayaran ini'
            ], 403);
        }

        $pembayaran->status_pembayaran = $request->status_pembayaran;
        $pembayaran->save();

        // Update status pendaftaran jika pembayaran terverifikasi
        if ($request->status_pembayaran === 'terverifikasi') {
            $pendaftaran = $pembayaran->pendaftaran;
            if ($pendaftaran) {
                $pendaftaran->status_pendaftaran = 'confirmed';
                $pendaftaran->save();
            }
        } elseif ($request->status_pembayaran === 'ditolak') {
             $pendaftaran = $pembayaran->pendaftaran;
             if ($pendaftaran) {
                 $pendaftaran->status_pendaftaran = 'ditolak';
                 $pendaftaran->save();
             }
        }

        return response()->json([
            'message' => 'Status pembayaran berhasil diperbarui',
            'data' => $this->transformPembayaran($pembayaran->load(['pendaftaran.user', 'pendaftaran.event', 'metodePembayaran']))
        ]);
    }

    // melihat semua pembayaran
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Pembayaran::with(['pendaftaran.user', 'pendaftaran.event', 'metodePembayaran']);

        // Jika role adalah penyelenggara, filter hanya event miliknya
        if ($user && $user->role === 'penyelenggara') {
            $query->whereHas('pendaftaran.event', function ($q) use ($user) {
                $q->where('user_id', $user->id_user);
            });
        }

        $data = $query->orderBy('created_at', 'desc')->get()->map(function ($pembayaran) {
            return $this->transformPembayaran($pembayaran);
        });

        return response()->json($data);
    }

    // membuat pembayaran
    public function store(Request $request)
    {
        $buktiPath = null;

        // cek apakah ada file bukti pembayaran
        if ($request->hasFile('bukti_pembayaran')) {

            $file = $request->file('bukti_pembayaran');

            $filename = time().'_'.$file->getClientOriginalName();

            // simpan ke storage
            $buktiPath = $file->storeAs('bukti_pembayaran', $filename, 'public');
        }


        $isCustom = !is_numeric($request->metode_pembayaran_id);

        $pembayaran = Pembayaran::create([
            'pendaftaran_id' => $request->pendaftaran_id,
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_pembayaran_id' => $isCustom ? null : $request->metode_pembayaran_id,
            'metode_pembayaran_custom' => $isCustom ? $request->metode_pembayaran_id : null,
            'bukti_pembayaran' => $buktiPath,
            'status_pembayaran' => 'pending'
        ]);

        return response()->json([
            'message' => 'Pembayaran berhasil dibuat',
            'data' => $this->transformPembayaran($pembayaran->load(['pendaftaran.user', 'pendaftaran.event', 'metodePembayaran']))
        ]);
    }

    // melihat pembayaran berdasarkan id
    public function show($id)
    {
        $pembayaran = Pembayaran::with(['pendaftaran.user', 'pendaftaran.event', 'metodePembayaran'])->find($id);

        if(!$pembayaran){
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ],404);
        }

        return response()->json($this->transformPembayaran($pembayaran));
    }
}
