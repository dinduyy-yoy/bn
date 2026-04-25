<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OTPVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;

class OTPController extends Controller
{
    /**
     * Verifikasi OTP dan aktifkan akun user
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'otp.required'   => 'OTP wajib diisi',
            'otp.size'       => 'OTP harus 6 digit',
        ]);

        // Cari OTP yang valid (belum diverifikasi, belum expired)
        $otpRecord = OTPVerification::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            // Cek apakah OTP ada tapi sudah expired
            $expiredRecord = OTPVerification::where('email', $validated['email'])
                ->where('otp', $validated['otp'])
                ->where('verified', false)
                ->where('expires_at', '<=', Carbon::now())
                ->first();

            if ($expiredRecord) {
                return response()->json([
                    'message' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.',
                    'errors'  => ['otp' => ['Kode OTP sudah kadaluarsa']],
                ], 400);
            }

            return response()->json([
                'message' => 'Kode OTP tidak valid',
                'errors'  => ['otp' => ['Kode OTP tidak valid']],
            ], 400);
        }

        // Tandai OTP sebagai verified
        $otpRecord->update(['verified' => true]);

        // Aktifkan user
        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        }

        // Hapus semua OTP untuk email ini
        OTPVerification::where('email', $validated['email'])->delete();

        // Hapus token lama & generate token baru untuk login otomatis
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Verifikasi berhasil! Anda telah masuk.',
            'data'    => [
                'id_user'            => $user->id_user,
                'nama_lengkap'       => $user->nama_lengkap,
                'email'              => $user->email,
                'no_hp'              => $user->no_hp,
                'kategori_pendaftar' => $user->kategori_pendaftar,
                'role'               => $user->role,
                'email_verified_at'  => $user->email_verified_at,
            ],
            'token'   => $token,
        ]);
    }

    /**
     * Kirim ulang OTP — expired 5 menit
     */
    public function resend(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak terdaftar',
                'errors'  => ['email' => ['Email tidak ditemukan']],
            ], 404);
        }

        // Jika sudah terverifikasi, tidak perlu kirim OTP
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email sudah terverifikasi. Silakan login.',
            ], 400);
        }

        // Cek rate limiting — jangan spam, minimal 1 menit antar pengiriman
        $latestOtp = OTPVerification::where('email', $user->email)
            ->where('created_at', '>=', now()->subMinute())
            ->first();

        if ($latestOtp) {
            return response()->json([
                'message' => 'Mohon tunggu 1 menit sebelum mengirim ulang OTP',
                'errors'  => ['otp' => ['Terlalu banyak permintaan']],
            ], 429);
        }

        // Generate OTP baru
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama
        OTPVerification::where('email', $user->email)->delete();

        // Simpan OTP baru — expired 5 menit
        OTPVerification::create([
            'email'      => $user->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
            'verified'   => false,
        ]);

        // Kirim email OTP
        try {
            Mail::to($user->email)->send(new OTPMail($user->nama_lengkap, $otp));
        } catch (\Exception $e) {
            \Log::error('Gagal kirim ulang OTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengirim OTP. Coba beberapa saat lagi.',
            ], 500);
        }

        return response()->json([
            'message' => 'Kode OTP baru telah dikirim ke email Anda. Berlaku selama 5 menit.',
        ]);
    }
}
