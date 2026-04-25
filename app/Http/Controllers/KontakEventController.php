<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontakEvent;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReplyContact;

class KontakEventController extends Controller
{
     // kirim pengajuan event (tanpa login)
    public function store(Request $request)
    {
        $kontak = KontakEvent::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'judul_event' => $request->judul_event,
            'deskripsi_event' => $request->deskripsi_event ?? '',
            'pesan' => $request->pesan,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Pengajuan event berhasil dikirim',
            'data' => $kontak
        ],201);
    }

    // admin melihat semua pengajuan
    public function index()
    {
        return response()->json(KontakEvent::all(),200);
    }

    // admin update status
    public function update(Request $request, $id)
    {
       $kontak = KontakEvent::find($id);

    if(!$kontak){
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ],404);
    }

    $kontak->update([
        'nama' => $request->nama ?? $kontak->nama,
        'email' => $request->email ?? $kontak->email,
        'no_hp' => $request->no_hp ?? $kontak->no_hp,
        'judul_event' => $request->judul_event ?? $kontak->judul_event,
        'deskripsi_event' => $request->deskripsi_event ?? $kontak->deskripsi_event,
        'pesan' => $request->pesan ?? $kontak->pesan,
        'status' => $request->status ?? $kontak->status,
        'balasan' => $request->balasan ?? $kontak->balasan,
        'replied_at' => $request->replied_at ?? $kontak->replied_at,
    ]);

    return response()->json([
        'message' => 'Data berhasil diupdate',
        'data' => $kontak
    ],200);
    }

    // admin balas pesan
    public function reply(Request $request, $id)
    {
        $kontak = KontakEvent::find($id);

        if(!$kontak){
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ],404);
        }

        // Kirim Email Nyata
        try {
            Mail::to($kontak->email)->send(new ReplyContact([
                'nama' => $kontak->nama,
                'subjek' => $kontak->judul_event ?: 'Pesan Kontak',
                'pesan_asli' => $kontak->pesan,
                'balasan' => $request->balasan
            ]));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }

        $kontak->update([
            'balasan' => $request->balasan,
            'replied_at' => now(),
            'status' => 'replied'
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dibalas dan email terkirim',
            'data' => $kontak
        ],200);
    }

    // admin hapus pesan
    public function destroy($id)
    {
        $kontak = KontakEvent::find($id);

        if(!$kontak){
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ],404);
        }

        $kontak->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ],200);
    }
}

