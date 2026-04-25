<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita;

class BeritaController extends Controller
{
    // Get semua berita
    public function index()
    {
        $berita = Berita::with('kategori')->get();
        return response()->json($berita, 200);
    }

    // Get berita by ID
    public function show($id)
    {
        $berita = Berita::with('kategori')->find($id);

        if (!$berita) {
            return response()->json([
                'message' => 'Berita tidak ditemukan'
            ], 404);
        }

        return response()->json($berita, 200);
    }

    // Tambah berita baru
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_berita,id',
            'sumber' => 'required|url|max:255',
            'ringkasan' => 'required|string',
            'konten' => 'required|string',
            'gambar' => 'nullable|string|max:255',
            'tanggal' => 'required|date'
        ]);

        $berita = Berita::create([
            'judul' => $request->judul,
            'kategori_id' => $request->kategori_id,
            'sumber' => $request->sumber,
            'ringkasan' => $request->ringkasan,
            'konten' => $request->konten,
            'gambar' => $request->gambar,
            'tanggal' => $request->tanggal
        ]);

        return response()->json([
            'message' => 'Berita berhasil ditambahkan',
            'data' => $berita
        ], 201);
    }

    // Update berita
    public function update(Request $request, $id)
    {
        $berita = Berita::find($id);

        if (!$berita) {
            return response()->json([
                'message' => 'Berita tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_berita,id',
            'sumber' => 'required|url|max:255',
            'ringkasan' => 'required|string',
            'konten' => 'required|string',
            'gambar' => 'nullable|string|max:255',
            'tanggal' => 'required|date'
        ]);

        $berita->update([
            'judul' => $request->judul,
            'kategori_id' => $request->kategori_id,
            'sumber' => $request->sumber,
            'ringkasan' => $request->ringkasan,
            'konten' => $request->konten,
            'gambar' => $request->gambar,
            'tanggal' => $request->tanggal
        ]);

        return response()->json([
            'message' => 'Berita berhasil diupdate',
            'data' => $berita
        ], 200);
    }

    // Hapus berita
    public function destroy($id)
    {
        $berita = Berita::find($id);

        if (!$berita) {
            return response()->json([
                'message' => 'Berita tidak ditemukan'
            ], 404);
        }

        $berita->delete();

        return response()->json([
            'message' => 'Berita berhasil dihapus'
        ], 200);
    }
}
