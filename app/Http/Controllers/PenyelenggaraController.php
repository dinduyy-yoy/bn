<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\PendaftaranEvent;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenyelenggaraController extends Controller
{
    /**
     * Get semua penyelenggara (role = penyelenggara)
     */
    public function index()
    {
        $penyelenggara = User::where('role', 'penyelenggara')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($penyelenggara, 200);
    }

    /**
     * Dashboard stats untuk penyelenggara
     */
    public function dashboardStats()
    {
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $userId = $user->id_user;

        $today = Carbon::today();
        $currentYear = Carbon::now()->year;

        $baseEventQuery = Event::where('user_id', $userId);
        $eventIds = (clone $baseEventQuery)->pluck('id');

        // 1. Total event
        $totalEvent = (clone $baseEventQuery)->count();

        // 2. Event aktif (tanggal >= hari ini)
        $eventAktif = (clone $baseEventQuery)->whereDate('tanggal', '>=', $today)->count();

        // 3. Event selesai (tanggal < hari ini)
        $eventSelesai = (clone $baseEventQuery)->whereDate('tanggal', '<', $today)->count();

        // 4. Total peserta terdaftar
        $totalPeserta = PendaftaranEvent::whereIn('event_id', $eventIds)->count();

        // 5. Tren event per bulan (tahun berjalan)
        $eventsPerMonth = (clone $baseEventQuery)->select(
            DB::raw('MONTH(tanggal) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('tanggal', $currentYear)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        // 6. Tren peserta per bulan (tahun berjalan)
        $pesertaPerMonth = PendaftaranEvent::select(
            DB::raw('MONTH(tanggal_daftar) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->whereIn('event_id', $eventIds)
        ->whereYear('tanggal_daftar', $currentYear)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'name'    => $bulanLabels[$i - 1],
                'event'   => $eventsPerMonth->has($i) ? $eventsPerMonth[$i]->total : 0,
                'peserta' => $pesertaPerMonth->has($i) ? $pesertaPerMonth[$i]->total : 0,
            ];
        }

        // 7. Distribusi kategori event
        $kategoriData = (clone $baseEventQuery)->select('kategori_id', DB::raw('COUNT(*) as total'))
            ->with('kategori')
            ->groupBy('kategori_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => optional($item->kategori)->nama_kategori ?? 'Lainnya',
                    'value' => $item->total,
                ];
            })
            ->values();

        // 8. 5 Event terbaru beserta jumlah peserta
        $recentEvents = (clone $baseEventQuery)->with('kategori')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ev) use ($today) {
                return [
                    'id'       => $ev->id,
                    'nama'     => $ev->nama_event,
                    'tanggal'  => Carbon::parse($ev->tanggal)->translatedFormat('d M Y'),
                    'lokasi'   => $ev->lokasi,
                    'kategori' => optional($ev->kategori)->nama_kategori ?? 'Lainnya',
                    'peserta'  => PendaftaranEvent::where('event_id', $ev->id)->count(),
                    'status'   => Carbon::parse($ev->tanggal)->gte($today) ? 'aktif' : 'selesai',
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_event'    => $totalEvent,
                'total_peserta'  => $totalPeserta,
                'event_aktif'    => $eventAktif,
                'event_selesai'  => $eventSelesai,
                'monthly_data'   => $monthlyData,
                'kategori_data'  => $kategoriData,
                'recent_events'  => $recentEvents,
            ],
        ], 200);
    }

    /**
     * Tambah penyelenggara baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_hp' => 'required|string|min:10|max:15',
            'kategori_pendaftar' => 'required|string|in:Unit Kerja,Mahasiswa,Komunitas',
            'password' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $penyelenggara = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'kategori_pendaftar' => $validated['kategori_pendaftar'],
            'password' => Hash::make($validated['password']),
            'role' => 'penyelenggara',
            'email_verified_at' => $validated['status'] === 'aktif' ? now() : null,
        ]);

        return response()->json([
            'message' => 'Penyelenggara berhasil ditambahkan',
            'data' => $penyelenggara
        ], 201);
    }

    /**
     * Update penyelenggara
     */
    public function update(Request $request, $id)
    {
        $penyelenggara = User::where('role', 'penyelenggara')->find($id);

        if (!$penyelenggara) {
            return response()->json([
                'message' => 'Penyelenggara tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $id . ',id_user',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',id_user',
            'no_hp' => 'required|string|min:10|max:15',
            'kategori_pendaftar' => 'required|string|in:Unit Kerja,Mahasiswa,Komunitas',
            'password' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $updateData = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'kategori_pendaftar' => $validated['kategori_pendaftar'],
        ];

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Update status (email_verified_at sebagai indikator aktif/nonaktif)
        if ($validated['status'] === 'aktif') {
            $updateData['email_verified_at'] = now();
        } else {
            $updateData['email_verified_at'] = null;
        }

        $penyelenggara->update($updateData);

        return response()->json([
            'message' => 'Penyelenggara berhasil diupdate',
            'data' => $penyelenggara
        ], 200);
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus($id)
    {
        $penyelenggara = User::where('role', 'penyelenggara')->find($id);

        if (!$penyelenggara) {
            return response()->json([
                'message' => 'Penyelenggara tidak ditemukan'
            ], 404);
        }

        // Toggle email_verified_at untuk status aktif/nonaktif
        if ($penyelenggara->email_verified_at) {
            $penyelenggara->update(['email_verified_at' => null]);
            $status = 'nonaktif';
        } else {
            $penyelenggara->update(['email_verified_at' => now()]);
            $status = 'aktif';
        }

        return response()->json([
            'message' => 'Status berhasil diubah',
            'data' => $penyelenggara,
            'status' => $status
        ], 200);
    }

    /**
     * Hapus penyelenggara
     */
    public function destroy($id)
    {
        \Log::info("Attempting to delete penyelenggara ID: " . $id);
        $penyelenggara = User::where('role', 'penyelenggara')->find($id);

        if (!$penyelenggara) {
            return response()->json([
                'message' => 'Penyelenggara tidak ditemukan'
            ], 404);
        }

        // Manual Cascade Deletion
        DB::beginTransaction();
        try {
            // 1. Cari semua event yang diselenggarakan oleh user ini
            $events = Event::where('user_id', $id)->get();

            foreach ($events as $event) {
                // a. Ambil pendaftaran untuk event ini
                $pendaftaranIds = PendaftaranEvent::where('event_id', $event->id)->pluck('id');
                // b. Hapus pembayaran terkait
                Pembayaran::whereIn('pendaftaran_id', $pendaftaranIds)->delete();
                // c. Hapus pendaftaran terkait
                PendaftaranEvent::where('event_id', $event->id)->delete();
                // d. Hapus event itu sendiri
                $event->delete();
            }

            // 2. Hapus data lain yang terkait dengan user ini sebagai peserta/umum
            // a. Hapus pendaftaran di mana user ini adalah peserta
            $pendaftaranUserIds = PendaftaranEvent::where('user_id', $id)->pluck('id');
            Pembayaran::whereIn('pendaftaran_id', $pendaftaranUserIds)->delete();
            PendaftaranEvent::where('user_id', $id)->delete();

            // b. Hapus OTP
            DB::table('otp_verifications')->where('email', $penyelenggara->email)->delete();

            // 3. Hapus penyelenggara
            $penyelenggara->delete();

            DB::commit();

            return response()->json([
                'message' => 'Penyelenggara berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus penyelenggara: ' . $e->getMessage()
            ], 500);
        }
    }
}
