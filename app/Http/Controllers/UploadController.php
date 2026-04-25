<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadPoster(Request $request)
    {
        $request->validate([
            'foto_event' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('foto_event')) {
            $file = $request->file('foto_event');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('event'), $filename);

            return response()->json([
                'message' => 'Poster berhasil diupload',
                'filename' => $filename,
                'url' => url('event/' . $filename)
            ], 200);
        }

        return response()->json([
            'message' => 'Tidak ada file yang diupload'
        ], 400);
    }
}
