<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\PendaftaranEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Total Event
        $totalEvent = Event::count();

        // 2. Total User
        $totalUser = User::count();

        // 3. Total Penyelenggara
        $totalPenyelenggara = User::where('role', 'penyelenggara')->count();

        // 4. Total Peserta
        $totalPeserta = User::where('role', 'peserta')->count();

        // 5. Grafik Kegiatan per Bulan
        $eventsPerMonth = Event::select(
            DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as periode'),
            DB::raw('COUNT(*) as total_kegiatan')
        )
        ->groupBy('periode')
        ->orderBy('periode', 'asc')
        ->get();

        $formattedEvents = $eventsPerMonth->map(function ($item) {
            return [
                'name' => Carbon::parse($item->periode . '-01')->format('M Y'),
                'total' => $item->total_kegiatan
            ];
        });

        // 6. Grafik Peserta per Bulan
        $participantsPerMonth = PendaftaranEvent::select(
            DB::raw('DATE_FORMAT(tanggal_daftar, "%Y-%m") as periode'),
            DB::raw('COUNT(*) as total_peserta')
        )
        ->groupBy('periode')
        ->orderBy('periode', 'asc')
        ->get();

        $formattedParticipants = $participantsPerMonth->map(function ($item) {
            return [
                'name' => Carbon::parse($item->periode . '-01')->format('M Y'),
                'total' => $item->total_peserta
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_event' => $totalEvent,
                'total_user' => $totalUser,
                'total_penyelenggara' => $totalPenyelenggara,
                'total_peserta' => $totalPeserta,
                'events_per_month' => $formattedEvents,
                'participants_per_month' => $formattedParticipants
            ]
        ], 200);
    }

    /**
     * Mendapatkan semua user (semua role) untuk halaman Admin Pengguna
     */
    public function getAllUsers()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users, 200);
    }

    /**
     * Mendapatkan data laporan event dan penyelenggara
     */
    public function getLaporan()
    {
        $user = Auth::user();
        $query = Event::with(['kategori', 'penyelenggara']);

        if ($user && $user->role === 'penyelenggara') {
            $query->where('user_id', $user->id_user);
        }

        $events = $query->get()->map(function ($ev) {
            $pendaftar = PendaftaranEvent::where('event_id', $ev->id)->count();
            // Karena tidak ada field kehadiran spesifik, kita asumsikan yang status_pendaftaran = 'success' adalah hadir. 
            // Jika belum ada status success, kita beri nilai 0 atau simulasi (untuk sementara kita hitung yang statusnya success/hadir)
            $hadir = PendaftaranEvent::where('event_id', $ev->id)
                        ->whereIn('status_pendaftaran', ['success', 'hadir', 'confirmed'])
                        ->count();

            return [
                'id' => $ev->id,
                'nama_event' => $ev->nama_event,
                'penyelenggara' => optional($ev->penyelenggara)->nama_lengkap ?? 'Global Admin',
                'tanggal' => Carbon::parse($ev->tanggal)->format('Y-m-d'),
                'kategori' => optional($ev->kategori)->nama_kategori ?? 'Umum',
                'pendaftar' => $pendaftar,
                'hadir' => $hadir
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $events
        ], 200);
    }
}
