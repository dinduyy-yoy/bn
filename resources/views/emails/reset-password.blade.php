<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f0f4ff;
            color: #1a1a2e;
            padding: 40px 16px;
        }
        .wrapper {
            max-width: 560px;
            margin: 0 auto;
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(99, 102, 241, 0.12);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            padding: 40px 32px;
            text-align: center;
        }
        .header-icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 32px;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            margin-top: 6px;
        }
        .body {
            padding: 40px 32px;
        }
        .greeting {
            font-size: 16px;
            color: #374151;
            margin-bottom: 16px;
        }
        .greeting strong { color: #6366f1; }
        .description {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .btn-container {
            text-align: center;
            margin-bottom: 32px;
        }
        .btn-reset {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }
        .expire-note {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #0369a1;
        }
        .expire-note strong { color: #0c4a6e; }
        .url-fallback {
            margin-bottom: 24px;
        }
        .url-fallback p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        .url-box {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 11px;
            color: #374151;
            word-break: break-all;
            font-family: monospace;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }
        .warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 24px;
        }
        .warning p {
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
        }
        .footer {
            background: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p { font-size: 12px; color: #9ca3af; }
        .footer .brand { font-weight: 700; color: #6366f1; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="header-icon">🔑</div>
                <h1>Reset Password</h1>
                <p>IBIK Event Management System</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="greeting">
                    Halo, <strong>{{ $namaLengkap }}</strong>! 👋
                </p>
                <p class="description">
                    Kami menerima permintaan untuk mereset password akun IBIK Event Anda.
                    Klik tombol di bawah ini untuk membuat password baru.
                </p>

                <!-- CTA Button -->
                <div class="btn-container">
                    <a href="{{ $resetUrl }}" class="btn-reset">
                        🔓 Reset Password Saya
                    </a>
                </div>

                <!-- Expire Note -->
                <div class="expire-note">
                    ⏰ <strong>Perhatian:</strong> Link ini hanya berlaku selama <strong>60 menit</strong>.
                    Setelah itu, Anda perlu meminta link reset baru.
                </div>

                <!-- URL Fallback -->
                <div class="url-fallback">
                    <p>Jika tombol tidak berfungsi, salin tautan berikut ke browser Anda:</p>
                    <div class="url-box">{{ $resetUrl }}</div>
                </div>

                <hr class="divider" />

                <!-- Warning -->
                <div class="warning">
                    <p>
                        <strong>⚠️ Bukan Anda yang meminta?</strong><br />
                        Jika Anda tidak mereset password, abaikan email ini. 
                        Akun Anda tetap aman dan tidak ada perubahan yang akan terjadi.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} <span class="brand">IBIK Event</span>. Semua hak dilindungi.</p>
                <p style="margin-top:4px;">Email ini dikirim otomatis, mohon jangan membalas.</p>
            </div>
        </div>
    </div>
</body>
</html>
