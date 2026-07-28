<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - LSPro BRMP</title>
    
    <link rel="stylesheet" href="{{ asset('css/login_style.css') }}?v={{ time() }}">
    
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
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        <i class="fa-solid fa-eye toggle-password" style="position: absolute; left: auto !important; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8;" onclick="togglePasswordVisibility('password', this)"></i>
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
                    <label>Kebijakan Privasi</label>
                    <div style="height: 100px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 12px; color: #475569; background: #f8fafc; margin-bottom: 12px; line-height: 1.6;">
                        <strong>KEBIJAKAN PRIVASI - LAYANAN SERTIFIKASI PRODUK BBPM SDLP</strong><br><br>
                        Dengan mendaftar di sistem ini, Anda menyetujui ketentuan berikut:<br>
                        1. Data pribadi dan data perusahaan yang Anda berikan akan digunakan semata-mata untuk keperluan proses sertifikasi produk oleh LSPro BBPM SDLP.<br>
                        2. Kami menjaga kerahasiaan seluruh informasi yang Anda sampaikan dan tidak akan membagikannya kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh peraturan perundang-undangan yang berlaku.<br>
                        3. Seluruh dokumen yang diunggah akan disimpan secara aman dan hanya dapat diakses oleh personel berwenang dalam lingkup layanan sertifikasi.<br>
                        4. Anda berhak untuk meminta penghapusan data pribadi setelah proses sertifikasi selesai, dengan menghubungi kami melalui kontak resmi yang tersedia.
                    </div>
                    <label style="display: flex; align-items: flex-start; gap: 10px; font-weight: normal; cursor: pointer; font-size: 13px; color: #334155;">
                        <input type="checkbox" required style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                        <span>Saya telah membaca, memahami, dan menyetujui Kebijakan Privasi di atas.</span>
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

    <script>
        function togglePasswordVisibility(inputId, iconElement) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
