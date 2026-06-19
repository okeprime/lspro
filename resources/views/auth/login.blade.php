<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - LSPro BRMP</title>
    
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

        <div style="margin-top: 20px;">
            <h1 class="auth-title">Selamat Datang Kembali</h1>
            <p class="auth-subtitle">Silakan masukkan email dan password Anda untuk masuk ke sistem.</p>

            @if ($errors->any())
                <div style="background: #fee2e2; color: #ef4444; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border-left: 4px solid #ef4444;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>E-mail *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="contoh@mail.dev" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div style="text-align: right; margin-bottom: 20px;">
                    <a href="#" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-auth">Masuk Sekarang &rarr;</button>
            </form>

            <p style="text-align: center; margin-top: 30px; font-size: 14px; color: #64748b;">
                Belum punya akun? <a href="{{ route('register') }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">Daftar di sini</a>
            </p>
        </div>
    </div>

    <div class="auth-right"></div>

</body>
</html>