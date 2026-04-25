<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balasan Pesan - KESAVENT</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            padding: 40px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 2px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
        }
        .reply-box {
            background-color: #f8fafc;
            border-left: 4px solid #7c3aed;
            padding: 20px;
            border-radius: 0 12px 12px 0;
            margin: 25px 0;
        }
        .reply-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #7c3aed;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .reply-text {
            font-size: 16px;
            color: #1e293b;
        }
        .original-message {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        .original-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 10px;
        }
        .original-content {
            font-size: 14px;
            color: #94a3b8;
            font-style: italic;
        }
        .footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            border-top: 1px solid #f1f5f9;
        }
        .footer strong {
            color: #1e293b;
        }
        .footer-logo {
            margin-bottom: 15px;
            font-weight: 800;
            color: #7c3aed;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KESAVENT</h1>
            <p>Pusat Manajemen Event Terpercaya</p>
        </div>
        
        <div class="content">
            <div class="greeting">Halo, {{ $nama }}!</div>
            
            <p>Terima kasih telah menghubungi kami. Tim admin kami telah meninjau pesan Anda dan berikut adalah balasannya:</p>
            
            <div class="reply-box">
                <span class="reply-label">Balasan Admin</span>
                <div class="reply-text">
                    {{ $balasan }}
                </div>
            </div>
            
            <div class="original-message">
                <div class="original-label">Informasi Pesan Asli Anda:</div>
                <div style="margin-bottom: 5px;"><strong>Subjek:</strong> {{ $subjek }}</div>
                <div class="original-content">
                    "{{ $pesan_asli }}"
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-logo">KESAVENT</div>
            <p>Pesan ini dikirim secara otomatis. Mohon tidak membalas langsung ke alamat email ini.</p>
            <p style="margin-top: 15px;">Salam hangat,<br><strong>Admin KESAVENT</strong></p>
        </div>
    </div>
</body>
</html>
