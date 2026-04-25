<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PendaftaranEventController;
use App\Http\Controllers\KontakEventController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\KategoriBeritaController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\PenyelenggaraController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ─── Settings Routes ────────────────────────────────────────────────────────
Route::get('/settings', [SettingController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/settings', [SettingController::class, 'update']);
});

// ─── Auth Routes (Public) ──────────────────────────────────────────────────
Route::post('/register',         [AuthController::class, 'register']);
Route::post('/login',            [AuthController::class, 'login']);
Route::post('/forgot-password',  [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',   [AuthController::class, 'resetPassword']);

// OTP Routes
Route::post('/otp/verify',  [OTPController::class, 'verify']);
Route::post('/otp/resend',  [OTPController::class, 'resend']);

// ─── Auth Routes (Protected) ───────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/me',       [AuthController::class, 'me']);
    
    // User Profile
    Route::post('/user/profile',  [AuthController::class, 'updateProfile']);
    Route::put('/user/password',  [AuthController::class, 'updatePassword']);
    Route::delete('/user/account', [AuthController::class, 'deleteAccount']);

    // Admin Dashboard
    Route::get('/admin/dashboard-stats', [AdminDashboardController::class, 'index']);
    // Admin: semua user
    Route::get('/admin/users', [AdminDashboardController::class, 'getAllUsers']);
    // Admin: laporan
    Route::get('/admin/laporan', [AdminDashboardController::class, 'getLaporan']);
});

// ─── Event Routes ──────────────────────────────────────────────────────────
Route::get('/event',       [EventController::class, 'index']);
Route::get('/event/{id}',  [EventController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/event',          [EventController::class, 'store']);
    Route::put('/event/{id}',      [EventController::class, 'update']);
    Route::delete('/event/{id}',   [EventController::class, 'destroy']);
});

// ─── Pendaftaran Event Routes ──────────────────────────────────────────────
Route::post('/daftar-event',        [PendaftaranEventController::class, 'store']);
Route::get('/daftar-event',         [PendaftaranEventController::class, 'index']);
Route::get('/daftar-event/{id}',    [PendaftaranEventController::class, 'show']);
Route::put('/daftar-event/{id}',    [PendaftaranEventController::class, 'update']);
Route::delete('/daftar-event/{id}', [PendaftaranEventController::class, 'destroy']);

// ─── Kontak Event Routes ───────────────────────────────────────────────────
Route::post('/kontak-event',       [KontakEventController::class, 'store']);
Route::get('/kontak-event',        [KontakEventController::class, 'index']);
Route::put('/kontak-event/{id}',   [KontakEventController::class, 'update']);
Route::post('/kontak-event/{id}/reply', [KontakEventController::class, 'reply']);
Route::delete('/kontak-event/{id}', [KontakEventController::class, 'destroy']);


// ─── Pembayaran Routes ─────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pembayaran',                    [PembayaranController::class, 'index']);
    Route::post('/pembayaran',                   [PembayaranController::class, 'store']);
    Route::get('/pembayaran/{id}',               [PembayaranController::class, 'show']);
    Route::put('/pembayaran/{id}/verifikasi',    [PembayaranController::class, 'verifikasi']);
});

// ─── Metode Pembayaran Routes ──────────────────────────────────────────────
Route::get('/metode-pembayaran',       [MetodePembayaranController::class, 'index']);
Route::post('/metode-pembayaran',      [MetodePembayaranController::class, 'store']);
Route::get('/metode-pembayaran/{id}',  [MetodePembayaranController::class, 'show']);

// ─── Kategori Routes ───────────────────────────────────────────────────────
Route::get('/kategori', [KategoriController::class, 'index']);

// ─── Upload Routes ─────────────────────────────────────────────────────────
Route::post('/upload-poster', [UploadController::class, 'uploadPoster']);

// ─── Berita Routes ─────────────────────────────────────────────────────────
Route::get('/berita', [BeritaController::class, 'index']);
Route::get('/berita/{id}', [BeritaController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/berita', [BeritaController::class, 'store']);
    Route::put('/berita/{id}', [BeritaController::class, 'update']);
    Route::delete('/berita/{id}', [BeritaController::class, 'destroy']);
});

// ─── Kategori Berita Routes ────────────────────────────────────────────────
Route::get('/kategori-berita', [KategoriBeritaController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/kategori-berita', [KategoriBeritaController::class, 'store']);
    Route::put('/kategori-berita/{id}', [KategoriBeritaController::class, 'update']);
    Route::delete('/kategori-berita/{id}', [KategoriBeritaController::class, 'destroy']);
});

// ─── Penyelenggara Routes ─────────────────────────────────────────────────
Route::get('/penyelenggara', [PenyelenggaraController::class, 'index']);
Route::get('/penyelenggara/dashboard-stats', [PenyelenggaraController::class, 'dashboardStats']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/penyelenggara', [PenyelenggaraController::class, 'store']);
    Route::put('/penyelenggara/{id}', [PenyelenggaraController::class, 'update']);
    Route::put('/penyelenggara/{id}/toggle-status', [PenyelenggaraController::class, 'toggleStatus']);
    Route::delete('/penyelenggara/{id}', [PenyelenggaraController::class, 'destroy']);
    
    // ─── Sertifikat Routes ──────────────────────────────────────────────────
    // Penyelenggara
    Route::post('/sertifikat/template/{event_id}', [App\Http\Controllers\SertifikatController::class, 'uploadTemplate']);
    Route::delete('/sertifikat/template/{event_id}', [App\Http\Controllers\SertifikatController::class, 'deleteTemplate']);
    Route::post('/sertifikat/generate/{event_id}', [App\Http\Controllers\SertifikatController::class, 'generateForEvent']);
    // User
    Route::get('/sertifikat/saya', [App\Http\Controllers\SertifikatController::class, 'indexUser']);
});
