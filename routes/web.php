<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
Route::get('/register/success', [RegisterController::class, 'showRegistrationSuccess'])->name('register.success');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    
    return redirect()->route('login')
        ->with('success', 'Email berhasil diverifikasi! Silakan login.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    
    return back()->with('message', 'Link verifikasi telah dikirim kembali!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Temporary login route (placeholder)
Route::get('/login', function () {
    return '<h1>Login Page - Coming Soon</h1><p><a href="' . route('register') . '">Go to Register</a></p>';
})->name('login');

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    return redirect()->route('login');
})->name('logout');
