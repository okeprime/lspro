<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - LSPro BRMP</title>
    
    <link rel="stylesheet" href="{{ asset('css/login_style.css') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body>

    <div class="auth-left">
        <div class="auth-header">
            <img src="{{ asset('assets/kementan.png') }}" alt="Logo Kementan">
            <h2>BALAI PERAKITAN DAN PENGUJIAN LINGKUNGAN PERTANIAN<br>
                <span style="font-size: 10px; font-weight: normal; color: #64748b;">BADAN PERAKITAN DAN MODERNISASI PERTANIAN</span>
            </h2>
        </div>

        <div>
            <h1 class="auth-title">Buat Akun</h1>
            <p class="auth-subtitle">Lengkapi formulir di bawah ini untuk mendapatkan akses ke sistem Sertifikasi LSPro.</p>

            @if ($errors->any())
                <div style="background: #fee2e2; color: #ef4444; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border-left: 4px solid #ef4444;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> Pastikan semua data diisi dengan benar.
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Perusahaan/Instansi *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-building"></i>
                        <input type="text" name="nama_perusahaan" class="form-control" placeholder="Contoh: PT. Alam Jaya" value="{{ old('nama_perusahaan') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Penghubung (PIC) *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="nama_penghubung" class="form-control" placeholder="Contoh: John Doe" value="{{ old('nama_penghubung') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>No. Telp/HP *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('no_telp') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>E-mail *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="contoh@mail.dev" value="{{ old('email') }}" required>
                    </div>
                    @error('email') <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    @error('password') <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap *</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat perusahaan..." value="{{ old('alamat') }}" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <label>Pakta Integritas</label>
                    <div style="height: 85px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 12px; color: #475569; background: #f8fafc; margin-bottom: 12px; line-height: 1.6;">
                        <strong>KEMENTERIAN PERTANIAN - PAKTA INTEGRITAS</strong><br>
                        Saya yang mendaftar di sistem ini menyatakan sebagai berikut:<br>
                        1. Berperan secara pro aktif dalam upaya pencegahan dan pemberantasan Korupsi, Kolusi, dan Nepotisme serta tidak melibatkan diri dalam perbuatan tercela.<br>
                        2. Tidak meminta atau menerima pemberian secara langsung atau tidak langsung berupa suap, hadiah, bantuan, atau bentuk lainnya yang tidak sesuai dengan ketentuan yang berlaku.
                    </div>
                    <label style="display: flex; align-items: flex-start; gap: 10px; font-weight: normal; cursor: pointer; font-size: 13px; color: #334155;">
                        <input type="checkbox" required style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                        <span>Saya telah membaca, memahami, dan menyetujui isi Pakta Integritas di atas.</span>
                    </label>
                </div>

                <button type="submit" class="btn-auth">Daftar Sekarang &rarr;</button>
            </form>

            <p style="text-align: center; margin-top: 30px; font-size: 14px; color: #64748b;">
                Sudah punya akun? <a href="{{ route('login') }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">Masuk di sini</a>
            </p>
        </div>
    </div>

    <div class="auth-right"></div>

</body>
</html>