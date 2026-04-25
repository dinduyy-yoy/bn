# Sistem Registrasi dengan Verifikasi Email

## Fitur
- Form registrasi dengan validasi lengkap
- Verifikasi email otomatis setelah registrasi
- Link aktivasi yang dikirim ke email user
- Password hashing otomatis
- Halaman sukses registrasi dengan instruksi

## Setup Email Configuration

### 1. Konfigurasi .env
Edit file `.env` untuk konfigurasi email:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Untuk Gmail
- Aktifkan 2-Factor Authentication
- Buat App Password: https://myaccount.google.com/apppasswords
- Gunakan App Password (bukan password biasa)

### 3. Untuk Mailtrap (Testing)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=null
```

## Routes
- `GET /register` - Form registrasi
- `POST /register` - Proses registrasi
- `GET /register/success` - Halaman sukses registrasi
- `GET /email/verify` - Halaman verifikasi email (memerlukan login)
- `GET /email/verify/{id}/{hash}` - Proses verifikasi email
- `POST /email/verification-notification` - Kirim ulang email verifikasi

## Flow Proses Registrasi
1. User mengisi form registrasi
2. System validasi input dan simpan user baru
3. Email verifikasi dikirim otomatis
4. User diarahkan ke halaman sukses
5. User klik link verifikasi di email
6. Email terverifikasi, user bisa login

## Validasi Input
- **Nama Lengkap**: Required, max 255 karakter
- **Email**: Required, valid email, unique
- **Nomor HP**: Required, 10-15 digit, angka saja
- **Kategori**: Required (peserta/sponsor/panitia)
- **Password**: Required, min 8 karakter, konfirmasi password

## Security Features
- Password hashing dengan bcrypt
- CSRF protection
- Input validation
- Unique email validation
- Rate limiting untuk resend email (6x per menit)
- Signed URL untuk verifikasi email

## Testing
1. Akses `http://localhost:8000/register`
2. Isi form registrasi
3. Periksa email untuk verifikasi
4. Klik link verifikasi
5. Coba login (setelah membuat halaman login)

## Customization
- Edit `app/Notifications/VerifyEmailNotification.php` untuk custom email template
- Edit view files di `resources/views/auth/` untuk custom UI
- Tambah validasi di `RegisterController.php` untuk rules tambahan
