<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\PendaftaranEvent;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    private function transformEvent($event)
    {
        // foto_event_url sekarang dihandle otomatis oleh model (accessor)
        
        $event->category = optional($event->kategori)->nama_kategori ?? 'Tanpa Kategori';
        $event->penyelenggara_name = optional($event->penyelenggara)->nama_lengkap ?? 'Admin';
        return $event;
    }

    // READ semua data
    public function index()
    {
        $user = Auth::guard('sanctum')->user();
        $query = Event::with(['kategori', 'penyelenggara'])->withCount('pendaftaran');

        // Jika request membawa token dan rolenya penyelenggara, kembalikan hanya event miliknya
        if ($user && $user->role === 'penyelenggara') {
            $query->where('user_id', $user->id_user);
        }

        $events = $query->get()->map(function ($event) {
            return $this->transformEvent($event);
        });
        return response()->json($events, 200);
    }

    // READ berdasarkan id
    public function show($id)
    {
        $event = Event::with(['kategori', 'penyelenggara'])->find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        return response()->json($this->transformEvent($event), 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'penyelenggara'])) {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $request->validate([
            'event_type' => 'required|in:online,offline',
            'lokasi' => 'required_if:event_type,offline',
            'meeting_link' => 'required_if:event_type,online',
        ], [
            'lokasi.required_if' => 'Lokasi wajib diisi untuk event offline.',
            'meeting_link.required_if' => 'Link meeting wajib diisi untuk event online.',
        ]);

        $namaFile = 'default-event.jpg';

        if ($request->hasFile('foto_event')) {
            $file = $request->file('foto_event');
            $namaFile = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('event'), $namaFile);
        }

        $event = Event::create([
            'user_id' => $user->id_user,
            'nama_event' => $request->nama_event,
            'event_type' => $request->event_type,
            'kategori_id' => $request->kategori_id,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'lokasi' => $request->event_type === 'offline' ? $request->lokasi : null,
            'meeting_link' => $request->event_type === 'online' ? $request->meeting_link : null,
            'harga' => $request->harga ?? 0,
            'foto_event' => $namaFile,
            'custom_form_schema' => $request->custom_form_schema,
            'metode_pembayaran' => $request->metode_pembayaran,
            'detail_pembayaran' => $request->detail_pembayaran
        ]);

        return response()->json([
            'message' => 'Event berhasil ditambahkan',
            'data' => $this->transformEvent($event->load('kategori'))
        ], 201);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'penyelenggara'])) {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        if ($user->role === 'penyelenggara' && $event->user_id !== $user->id_user) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengubah event ini'
            ], 403);
        }

        $request->validate([
            'event_type' => 'sometimes|in:online,offline',
            'lokasi' => 'required_if:event_type,offline',
            'meeting_link' => 'required_if:event_type,online',
        ], [
            'lokasi.required_if' => 'Lokasi wajib diisi untuk event offline.',
            'meeting_link.required_if' => 'Link meeting wajib diisi untuk event online.',
        ]);

        $namaFile = $event->foto_event ?: 'default-event.jpg';

        if ($request->hasFile('foto_event')) {
            $file = $request->file('foto_event');
            $namaFile = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('event'), $namaFile);
        }

        $event->update([
            'nama_event' => $request->nama_event,
            'event_type' => $request->event_type ?? $event->event_type,
            'kategori_id' => $request->kategori_id,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'lokasi' => ($request->event_type ?? $event->event_type) === 'offline' ? $request->lokasi : null,
            'meeting_link' => ($request->event_type ?? $event->event_type) === 'online' ? $request->meeting_link : null,
            'harga' => $request->harga ?? $event->harga,
            'foto_event' => $namaFile,
            'custom_form_schema' => $request->custom_form_schema ?? $event->custom_form_schema,
            'metode_pembayaran' => $request->metode_pembayaran ?? $event->metode_pembayaran,
            'detail_pembayaran' => $request->detail_pembayaran ?? $event->detail_pembayaran
        ]);

        return response()->json([
            'message' => 'Event berhasil diupdate',
            'data' => $this->transformEvent($event->load('kategori'))
        ], 200);
    }

    // DELETE
    public function destroy($id)
    {
        \Log::info("Attempting to delete event ID: " . $id);
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'penyelenggara'])) {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        if ($user->role === 'penyelenggara' && $event->user_id !== $user->id_user) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus event ini'
            ], 403);
        }

        // Manual Cascade Deletion
        DB::beginTransaction();
        try {
            // 1. Ambil semua pendaftaran untuk event ini
            $pendaftaranIds = PendaftaranEvent::where('event_id', $id)->pluck('id');

            // 2. Hapus semua pembayaran terkait pendaftaran tersebut
            Pembayaran::whereIn('pendaftaran_id', $pendaftaranIds)->delete();

            // 3. Hapus semua pendaftaran
            PendaftaranEvent::where('event_id', $id)->delete();

            // 4. Hapus event
            $event->delete();

            DB::commit();

            return response()->json([
                'message' => 'Event berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus event: ' . $e->getMessage()
            ], 500);
        }
    }
}
