<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetodePembayaran;

class MetodePembayaranController extends Controller
{
    // melihat semua metode pembayaran
    public function index()
    {
        return response()->json(MetodePembayaran::all());
    }

    // menambah metode pembayaran
    public function store(Request $request)
    {
        $metode = MetodePembayaran::create([
            'nama_metode' => $request->nama_metode,
            'nomor_tujuan' => $request->nomor_tujuan,
            'atas_nama' => $request->atas_nama
        ]);

        return response()->json([
            'message' => 'Metode pembayaran berhasil ditambahkan',
            'data' => $metode
        ]);
    }

    // melihat detail metode
    public function show($id)
    {
        $metode = MetodePembayaran::find($id);

        if(!$metode){
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ],404);
        }

        return response()->json($metode);
    }
}
