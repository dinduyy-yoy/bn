<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kode Verifikasi OTP</title>
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
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%);
            padding: 40px 32px;
            text-align: center;
        }
        .header-icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.2);
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
            color: rgba(255,255,255,0.85);
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
        .greeting strong {
            color: #6366f1;
        }
        .description {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .otp-container {
            background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
            border: 2px dashed #c4b5fd;
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            margin-bottom: 32px;
        }
        .otp-label {
            font-size: 12px;
            font-weight: 600;
            color: #8b5cf6;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 12px;
            color: #4c1d95;
            font-family: 'Courier New', monospace;
        }
        .otp-timer {
            margin-top: 12px;
            font-size: 13px;
            color: #ef4444;
            font-weight: 500;
        }
        .otp-timer span {
            background: #fee2e2;
            padding: 4px 10px;
            border-radius: 20px;
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
        .warning strong { color: #78350f; }
        .footer-note {
            font-size: 13px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer {
            background: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            font-size: 12px;
            color: #9ca3af;
        }
        .footer .brand {
            font-weight: 700;
            color: #6366f1;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="header-icon">🔐</div>
                <h1>Verifikasi Email Anda</h1>
                <p>IBIK Event Management System</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="greeting">
                    Halo, <strong>{{ $namaLengkap }}</strong>! 👋
                </p>
                <p class="description">
                    Terima kasih telah mendaftar di <strong>IBIK Event</strong>. 
                    Gunakan kode OTP berikut untuk memverifikasi alamat email Anda dan mengaktifkan akun.
                </p>

                <!-- OTP Box -->
                <div class="otp-container">
                    <div class="otp-label">Kode Verifikasi OTP</div>
                    <div class="otp-code">{{ $otp }}</div>
                    <div class="otp-timer">
                        <span>⏱ Berlaku selama 5 menit</span>
                    </div>
                </div>

                <hr class="divider" />

                <!-- Warning -->
                <div class="warning">
                    <p>
                        <strong>⚠️ Jangan bagikan kode ini kepada siapapun.</strong><br />
                        Tim kami tidak akan pernah meminta kode OTP Anda. 
                        Jika Anda tidak mendaftar, abaikan email ini.
                    </p>
                </div>

                <p class="footer-note">
                    Jika Anda membutuhkan bantuan, silakan hubungi tim support kami.
                </p>
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
