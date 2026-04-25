<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Show the registration success page.
     */
    public function showRegistrationSuccess()
    {
        return view('auth.registration-success');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_hp' => 'required|string|max:15|min:10',
            'kategori_pendaftar' => 'required|string|in:Mahasiswa,Umum,Dosen',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'no_hp.required' => 'Nomor handphone wajib diisi',
            'no_hp.min' => 'Nomor handphone minimal 10 digit',
            'no_hp.max' => 'Nomor handphone maksimal 15 digit',
            'kategori_pendaftar.required' => 'Kategori pendaftar wajib dipilih',
            'kategori_pendaftar.in' => 'Kategori tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Simpan user baru
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'kategori_pendaftar' => $request->kategori_pendaftar,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);

        // Trigger event untuk mengirim email verifikasi
        event(new Registered($user));

        // Redirect dengan pesan sukses
        return redirect()->route('register.success')
            ->with('success', 'Registrasi berhasil! Silakan periksa email Anda untuk verifikasi.');
    }
}
