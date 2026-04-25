<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\OTPVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\OTPMail;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    private function formatUserResponse($user)
    {
        return [
            'id_user'            => $user->id_user,
            'nama_lengkap'       => $user->nama_lengkap,
            'username'           => $user->username,
            'email'              => $user->email,
            'no_hp'              => $user->no_hp,
            'kategori_pendaftar' => $user->kategori_pendaftar,
            'role'               => $user->role,
            'avatar'             => $user->avatar,
            'avatarUrl'          => $user->avatar ? url('avatars/' . $user->avatar) : null,
            'email_verified_at'  => $user->email_verified_at,
        ];
    }

    /**
     * Register user baru dan kirim OTP ke email
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'       => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp'              => ['required', 'string', 'min:10', 'max:15'],
            'kategori_pendaftar' => ['required', 'string', 'in:Mahasiswa,Umum,Dosen'],
            'password'           => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'nama_lengkap.required'       => 'Nama lengkap wajib diisi',
            'nama_lengkap.max'            => 'Nama lengkap maksimal 255 karakter',
            'email.required'              => 'Email wajib diisi',
            'email.email'                 => 'Format email tidak valid',
            'email.unique'                => 'Email sudah terdaftar',
            'no_hp.required'              => 'Nomor handphone wajib diisi',
            'no_hp.min'                   => 'Nomor handphone minimal 10 digit',
            'no_hp.max'                   => 'Nomor handphone maksimal 15 digit',
            'kategori_pendaftar.required' => 'Kategori pendaftar wajib dipilih',
            'kategori_pendaftar.in'       => 'Kategori tidak valid',
            'password.required'           => 'Password wajib diisi',
            'password.confirmed'          => 'Konfirmasi password tidak cocok',
            'password.min'                => 'Password minimal 8 karakter',
            'password.mixed_case'         => 'Password harus mengandung huruf besar dan kecil',
            'password.numbers'            => 'Password harus mengandung angka',
            'password.symbols'            => 'Password harus mengandung simbol (contoh: @, #, !)',
        ]);

        $validated['email']    = strtolower(trim($validated['email']));
        $validated['password'] = Hash::make($validated['password']);
        $validated['role']     = 'user';

        $user = User::create($validated);

        // Generate OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama untuk email ini
        OTPVerification::where('email', $user->email)->delete();

        // Simpan OTP baru — expired 5 menit
        OTPVerification::create([
            'email'      => $user->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
            'verified'   => false,
        ]);

        // Kirim OTP via email
        try {
            Mail::to($user->email)->send(new OTPMail($user->nama_lengkap, $otp));
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan registrasi
            \Log::error('Gagal kirim email OTP: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Registrasi berhasil! Silakan periksa email Anda untuk kode OTP.',
            'data'    => [
                'id_user'     => $user->id_user,
                'nama_lengkap' => $user->nama_lengkap,
                'email'       => $user->email,
            ],
        ], 201);
    }

    /**
     * Login — hanya diizinkan setelah verifikasi email
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = strtolower(trim($validated['email']));
        $password = $validated['password'];

        // Cek login via email atau username
        $user = User::where('email', $loginField)
                    ->orWhere('username', $loginField)
                    ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Pengecekan OTP/Verifikasi ditiadakan sesuai permintaan
        // User yang sudah terdaftar bisa langsung masuk jika kredensial benar

        // Hapus token lama (opsional, untuk single session)
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'data'    => $this->formatUserResponse($user),
            'token'   => $token,
        ]);
    }

    /**
     * Logout — hapus token aktif
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Data user yang sedang login
     */
    public function me(Request $request)
    {
        return response()->json([
            'data' => $this->formatUserResponse($request->user()),
        ]);
    }

    /**
     * Forgot Password — kirim link reset via email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
        ]);

        $email = strtolower(trim($request->email));
        $user  = User::where('email', $email)->first();

        // Selalu kembalikan respons sukses (security: jangan bocorkan eksistensi email)
        if (!$user) {
            return response()->json([
                'message' => 'Jika email terdaftar, link reset password telah dikirim.',
            ]);
        }

        // Hapus token lama untuk email ini
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Generate token unik
        $token = Str::random(64);

        // Simpan ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Kirim email berisi link reset
        $resetUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'))
            . '/reset-password?token=' . $token
            . '&email=' . urlencode($email);

        try {
            Mail::to($email)->send(new ResetPasswordMail($user->nama_lengkap, $resetUrl));
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email reset password: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Jika email terdaftar, link reset password telah dikirim.',
        ]);
    }

    /**
     * Reset Password — validasi token, simpan password baru
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'token'    => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'email.required'    => 'Email wajib diisi',
            'token.required'    => 'Token wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min'       => 'Password minimal 8 karakter',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan kecil',
            'password.numbers'   => 'Password harus mengandung angka',
            'password.symbols'   => 'Password harus mengandung simbol',
        ]);

        $email = strtolower(trim($validated['email']));

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Token reset password tidak valid atau sudah kadaluarsa',
                'errors'  => ['token' => ['Token tidak valid']],
            ], 400);
        }

        // Cek apakah token cocok
        if (!Hash::check($validated['token'], $record->token)) {
            return response()->json([
                'message' => 'Token reset password tidak valid',
                'errors'  => ['token' => ['Token tidak valid']],
            ], 400);
        }

        // Cek expired — 60 menit
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return response()->json([
                'message' => 'Token reset password sudah kadaluarsa. Silakan minta ulang.',
                'errors'  => ['token' => ['Token sudah kadaluarsa']],
            ], 400);
        }

        // Update password user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Hapus token reset dan semua session aktif
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password berhasil direset! Silakan login dengan password baru Anda.',
        ]);
    }

    /**
     * Update Profile User
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['nullable', 'string', 'max:15'],
            'avatar'       => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'avatar.image'         => 'File harus berupa gambar',
            'avatar.mimes'         => 'Format gambar harus jpeg, png, atau jpg',
            'avatar.max'           => 'Ukuran gambar maksimal 2MB',
        ]);

        $updateData = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'no_hp'        => $validated['no_hp'],
        ];

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar && file_exists(public_path('avatars/' . $user->avatar))) {
                unlink(public_path('avatars/' . $user->avatar));
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id_user . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('avatars'), $filename);
            $updateData['avatar'] = $filename;
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data'    => $this->formatUserResponse($user),
        ]);
    }

    /**
     * Update Password User
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required'     => 'Password baru wajib diisi',
            'new_password.confirmed'    => 'Konfirmasi password baru tidak cocok',
            'new_password.min'          => 'Password baru minimal 8 karakter',
            'new_password.mixed_case'   => 'Password harus mengandung huruf besar dan kecil',
            'new_password.numbers'      => 'Password harus mengandung angka',
            'new_password.symbols'      => 'Password harus mengandung simbol',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak sesuai',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui',
        ]);
    }

    /**
     * Delete Account User
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Password wajib diisi untuk konfirmasi',
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Password tidak sesuai',
            ], 400);
        }

        // Hapus semua token
        $user->tokens()->delete();
        // Hapus akun
        $user->delete();

        return response()->json([
            'message' => 'Akun berhasil dihapus',
        ]);
    }
}
