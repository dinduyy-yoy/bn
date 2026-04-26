<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class UploadController extends Controller
{
    public function uploadPoster(Request $request)
    {
        $request->validate([
            'foto_event' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('foto_event')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('foto_event')->getRealPath())->getSecurePath();

            return response()->json([
                'message' => 'Poster berhasil diupload',
                'filename' => $uploadedFileUrl,
                'url' => $uploadedFileUrl
            ], 200);
        }


        return response()->json([
            'message' => 'Tidak ada file yang diupload'
        ], 400);
    }
}
