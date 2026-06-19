<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - LSPro BRMP</title>
    
    <link rel="stylesheet" href="{{ asset('css/login_style.css') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body>

    <div class="auth-left">
        <div class="auth-header">
            <img src="{{ asset('assets/kementan.png') }}" alt="Logo Kementan">
            <h2>BALAI BESAR PERAKITAN DAN MODERNISASI SUMBER DAYA LAHAN PERTANIAN<br>
                <span style="font-size: 10px; font-weight: normal; color: #64748b;">BADAN PERAKITAN DAN MODERNISASI PERTANIAN</span>
            </h2>
        </div>

        <div>
            <h1 class="auth-title">Verifikasi Email Anda</h1>
            <p class="auth-subtitle">Silakan verifikasi alamat email Anda terlebih dahulu untuk mengakses layanan pengajuan sertifikasi LSPro.</p>

            @if (session('success'))
                <div style="background: #dcfce7; color: #15803d; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border-left: 4px solid #15803d;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('success') }}
                </div>
            @endif

            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px;">
                <i class="fa-solid fa-envelope-open-text" style="font-size: 40px; color: #7c3aed; margin-bottom: 15px;"></i>
                <p style="font-size: 14px; color: #334155; margin-bottom: 0;">Kami telah mengirimkan tautan verifikasi ke email yang Anda daftarkan. Silakan periksa kotak masuk (inbox) atau folder spam Anda.</p>
            </div>

            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="btn-auth">Kirim Ulang Email Verifikasi &rarr;</button>
            </form>

            <form action="{{ route('logout') }}" method="POST" style="margin-top: 15px;">
                @csrf
                <button type="submit" class="btn-auth" style="background: white; color: #64748b; border: 1.5px solid #e2e8f0; display: block; width: 100%;">Keluar (Logout)</button>
            </form>
            
        </div>
    </div>

    <div class="auth-right"></div>

</body>
</html>
