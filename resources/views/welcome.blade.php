<!DOCTYPE html>
<html lang="id" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPro BRMP SDLP - Kementerian Pertanian</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .landing-navbar {
            background-color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 12px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .nav-logo-text {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .nav-logo-title {
            font-size: 16px;
            font-weight: 800;
            color: #16a34a;
            line-height: 1.2;
        }
        .nav-link {
            font-size: 13.5px;
            font-weight: 600;
            color: #475569 !important;
            padding: 8px 14px !important;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #16a34a !important;
        }
        .nav-link:hover, .nav-link.active {
            color: #16a34a !important;
        }
        .btn-masuk {
            background-color: #16a34a;
            color: white;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 24px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-masuk:hover {
            background-color: #15803d;
            color: white;
            transform: translateY(-2px);
        }

        .footer-links li {
            margin-bottom: 10px;
        }
        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #fff;
        }

        /* Timeline Steps (for Survailen) */
        .alur-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .steps-flow { display: flex; align-items: flex-start; overflow-x: auto; padding-bottom: 4px; }
        .step-item  { display: flex; flex-direction: column; align-items: center; text-align: center; flex: 1; min-width: 100px; }
        .step-row   { display: flex; align-items: center; width: 100%; }
        .step-circle { width: 44px; height: 44px; border-radius: 50%; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3); }
        .step-circle.blue { background: #2563eb; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3); }
        .step-line  { flex: 1; height: 3px; background: #d1fae5; }
        .step-line.blue { background: #bfdbfe; }
        .step-label { font-size: 13px; font-weight: 600; color: #374151; margin-top: 10px; line-height: 1.4; }

        /* ===== HERO SECTION ===== */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: url('{{ asset('assets/bg-login-reg.jpg') }}');
            background-size: cover;
            background-position: center;
            padding-top: 80px; /* Offset navbar */
            color: white;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.92) 0%, rgba(4, 120, 87, 0.85) 100%);
        }
        .hero-content {
            position: relative;
            z-index: 1;
            padding: 40px 0;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            backdrop-filter: blur(4px);
            margin-bottom: 24px;
        }
        .hero h1 {
            font-size: clamp(32px, 5vw, 56px);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }
        .hero p {
            font-size: clamp(15px, 2vw, 18px);
            font-weight: 400;
            line-height: 1.7;
            opacity: 0.9;
            max-width: 800px;
            margin-bottom: 40px;
        }
        .btn-hero-primary {
            background-color: #22c55e;
            color: white;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
        }
        .btn-hero-primary:hover {
            background-color: #16a34a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(34, 197, 94, 0.3);
        }
        .btn-hero-outline {
            background-color: rgba(255,255,255,0.15);
            color: white;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.3s;
        }
        .btn-hero-outline:hover {
            background-color: rgba(255,255,255,0.25);
            color: white;
            transform: translateY(-2px);
        }

        /* ===== STATS ===== */
        .hero-stats {
            margin-top: 60px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }
        .stat-item h3 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .stat-item p {
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }

        /* ===== CONTENT SECTIONS ===== */
        .content-section {
            padding: 80px 0;
            background: #f8fafc;
        }
        .section-title {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
            text-align: center;
        }
        .section-subtitle {
            font-size: 16px;
            color: #64748b;
            text-align: center;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid #e2e8f0;
            border-top: 4px solid #16a34a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }
        .info-card.accent-blue  { border-top-color: #2563eb; }
        .info-card.accent-purple  { border-top-color: #8b5cf6; }
        .card-icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 20px;
        }
        .icon-green  { background: #dcfce7; color: #16a34a; }
        .icon-blue   { background: #dbeafe; color: #2563eb; }
        .icon-purple { background: #f3e8ff; color: #8b5cf6; }
        
        .info-card h3 { font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 12px; }
        .info-card p  { font-size: 14.5px; color: #475569; line-height: 1.7; margin: 0; }

        .alur-step-wrapper {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        /* FOOTER */
        footer {
            background: #0f172a;
            color: white;
            padding: 40px 0;
            text-align: center;
            font-size: 14px;
            color: #94a3b8;
        }
        footer .logo {
            color: white;
            font-weight: 800;
            font-size: 20px;
            margin-bottom: 10px;
        }
        footer .logo i { color: #22c55e; }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="landing-navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Brand -->
            <a href="#" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="{{ asset('assets/kementan.png') }}" alt="Logo Kementan" style="height: 42px; width: auto; object-fit: contain;">
                <div>
                    <div class="nav-logo-text">Kementerian Pertanian RI</div>
                    <div class="nav-logo-title">LSPro BRMP SDLP</div>
                </div>
            </a>

            <!-- Menu (Desktop) -->
            <ul class="nav d-none d-xl-flex align-items-center" id="landing-nav-menu">
                <li class="nav-item"><a href="#beranda" class="nav-link active">Beranda</a></li>
                <li class="nav-item"><a href="#tentang-lspro" class="nav-link">Tentang LSPro</a></li>
                <li class="nav-item"><a href="#visi-misi" class="nav-link">Visi dan Misi</a></li>
                <li class="nav-item"><a href="#ruang-lingkup" class="nav-link">Ruang Lingkup</a></li>
                <li class="nav-item"><a href="#alur-sertifikasi" class="nav-link">Alur Sertifikasi</a></li>
                <li class="nav-item"><a href="#alur-survailen" class="nav-link">Alur Survailen</a></li>
            </ul>

            <!-- Auth -->
            <div>
                @auth
                    @php
                        $role = trim(strtolower(auth()->user()->role));
                        $dashboardRoute = ($role === 'admin' || $role === 'superadmin') ? route('admin.dashboard') : route('client.dashboard');
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn-masuk">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-masuk">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero" id="beranda">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fa-solid fa-shield-check"></i>
                    Terakreditasi SNI ISO/IEC 17065:2012
                </div>
                <h1>Layanan Sertifikasi Produk<br>LSPro BRMP SDLP</h1>
                <p>Balai Besar Perakitan dan Modernisasi Sumber Daya Lahan Pertanian melayani jasa sertifikasi pupuk secara mandiri, profesional, tidak diskriminatif, menjaga kerahasiaan pelanggan, serta menjamin hasil sertifikasi yang didukung oleh personel yang kompeten dan profesional.</p>
                
                <div class="d-flex gap-3 flex-wrap">
                    @auth
                        <a href="{{ route('pengajuan.sertifikasi') }}" class="btn-hero-primary">
                            Ajukan Sertifikasi <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            Ajukan Sertifikasi <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endauth
                    <a href="#tentang-lspro" class="btn-hero-outline">
                        Pelajari LSPro
                    </a>
                </div>

                
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TENTANG KAMI -->
    <section id="tentang-lspro" class="content-section">
        <div class="container">
            <h2 class="section-title">Kenali Kami Lebih Dekat</h2>
            <p class="section-subtitle">Lembaga sertifikasi terpercaya untuk memajukan kualitas produk pertanian Indonesia.</p>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="card-icon icon-green">
                            <i class="fa-solid fa-landmark"></i>
                        </div>
                        <h3>Apa itu BRMP SDLP?</h3>
                        <p>Balai Besar Perakitan dan Modernisasi Sumber Daya Lahan Pertanian (BRMP SDLP) adalah unit pelaksana teknis di bawah Kementerian Pertanian yang bertugas dalam kegiatan penelitian, pengkajian, perakitan, dan modernisasi teknologi pertanian berbasis sumber daya lahan.</p>
                        <p>BRMP SDLP berkomitmen mendukung kedaulatan pangan nasional melalui inovasi teknologi yang meningkatkan produktivitas, efisiensi, dan keberlanjutan sektor pertanian Indonesia.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card accent-blue">
                        <div class="card-icon icon-blue">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h3>Apa itu LSPro?</h3>
                        <p>Lembaga Sertifikasi Produk (LSPro) BRMP SDLP adalah unit sertifikasi yang bertugas menilai kesesuaian produk pupuk dan pembenah tanah terhadap Standar Nasional Indonesia (SNI).</p>
                        <p>LSPro BRMP SDLP beroperasi sesuai SNI ISO/IEC 17065:2012 dan telah terakreditasi oleh Komite Akreditasi Nasional (KAN). Kami menjamin independensi, kerahasiaan, dan objektivitas dalam setiap proses sertifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VISI MISI -->
    <section id="visi-misi" class="content-section bg-white">
        <div class="container">
            <h2 class="section-title">Visi & Misi</h2>
            <p class="section-subtitle">Arah dan tujuan utama dari pelayanan kami.</p>

            <div class="info-card accent-purple">
                <div class="row g-5">
                    <div class="col-md-6">
                        <div class="d-flex gap-3">
                            <div class="card-icon icon-purple" style="flex-shrink:0;">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <div>
                                <h4 style="font-weight: 800; font-size: 18px; margin-bottom: 12px; color: #1e293b;">Visi</h4>
                                <p style="font-size: 15px; color: #475569; line-height: 1.7;">Menjadi lembaga sertifikasi produk pupuk dan pembenah tanah yang profesional, independen, dan terpercaya di tingkat nasional maupun internasional untuk mendukung pertanian yang berkelanjutan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3">
                            <div class="card-icon icon-purple" style="flex-shrink:0;">
                                <i class="fa-solid fa-rocket"></i>
                            </div>
                            <div>
                                <h4 style="font-weight: 800; font-size: 18px; margin-bottom: 12px; color: #1e293b;">Misi</h4>
                                <ul style="font-size: 15px; color: #475569; line-height: 1.7; padding-left: 20px;">
                                    <li>Menyelenggarakan proses sertifikasi yang objektif, transparan, dan tidak memihak.</li>
                                    <li>Meningkatkan kompetensi personel secara berkesinambungan.</li>
                                    <li>Memberikan pelayanan prima demi tercapainya kepuasan pelanggan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RUANG LINGKUP -->
    <section id="ruang-lingkup" class="content-section">
        <div class="container">
            <h2 class="section-title">Ruang Lingkup Sertifikasi</h2>
            <p class="section-subtitle">LSPro BRMP SDLP berfokus pada sarana produksi pertanian berikut:</p>

            <div class="row g-3 justify-content-center">
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;">
                        <i class="fa-solid fa-leaf mb-3" style="color: #16a34a; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk Organik</h4>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;">
                        <i class="fa-solid fa-flask mb-3" style="color: #2563eb; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk NPK</h4>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;">
                        <i class="fa-solid fa-vial mb-3" style="color: #f59e0b; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk Urea</h4>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;">
                        <i class="fa-solid fa-vial-virus mb-3" style="color: #8b5cf6; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk ZA</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR SERTIFIKASI -->
    <section id="alur-sertifikasi" class="content-section bg-white">
        <div class="container text-center">
            <h2 class="section-title">Alur Sertifikasi Tipe 5</h2>
            <p class="section-subtitle">Proses sertifikasi produk SNI di LSPro BRMP SDLP yang transparan dan terukur.</p>
            
            <div class="alur-box max-w-4xl mx-auto" style="max-width: 1000px; margin: 0 auto;">
                <div class="steps-flow mt-3 mb-2">
                    @php
                    $stepsSertifikasi = [
                        'Pengajuan Permohonan', 
                        'Tinjauan Permohonan & Dokumen', 
                        'Evaluasi (Audit & Pengujian)', 
                        'Tinjauan Hasil Evaluasi', 
                        'Keputusan Sertifikasi',
                        'Penerbitan SPPT SNI'
                    ];
                    @endphp
                    @foreach($stepsSertifikasi as $i => $s)
                    <div class="step-item">
                        <div class="step-row">
                            <div class="step-circle">{{ $i+1 }}</div>
                            @if(!$loop->last)<div class="step-line"></div>@endif
                        </div>
                        <span class="step-label">{{ $s }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR SURVAILEN -->
    <section id="alur-survailen" class="content-section bg-light">
        <div class="container text-center">
            <h2 class="section-title">Alur Survailen</h2>
            <p class="section-subtitle">Pengawasan berkala untuk memastikan konsistensi mutu produk yang telah bersertifikat SNI.</p>
            
            <div class="alur-box max-w-4xl mx-auto" style="max-width: 900px; margin: 0 auto;">
                <div class="steps-flow mt-3 mb-2">
                    @php
                    $stepsV = ['Pemberitahuan / Notifikasi', 'Persiapan Klien & Tinjauan Dokumen', 'Audit Lapangan & Uji Petik', 'Evaluasi Hasil Survailen', 'Keputusan Keberlanjutan Sertifikat'];
                    @endphp
                    @foreach($stepsV as $i => $s)
                    <div class="step-item">
                        <div class="step-row">
                            <div class="step-circle blue">{{ $i+1 }}</div>
                            @if(!$loop->last)<div class="step-line blue"></div>@endif
                        </div>
                        <span class="step-label">{{ $s }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="kontak" style="background-color: #0f172a; color: #94a3b8; padding: 60px 0 20px 0; font-size: 14px;">
        <div class="container">
            <div class="row gy-4 mb-5">
                <!-- Logo & Info -->
                <div class="col-lg-4 col-md-6 pe-lg-5">
                    <div class="logo d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/kementan.png') }}" alt="Logo Kementan" style="height: 40px; width: auto; margin-right: 12px; filter: drop-shadow(0 0 2px rgba(255,255,255,0.8));"> 
                        <span style="font-weight: 800; font-size: 18px; color: #fff;">LSPro BRMP SDLP</span>
                    </div>
                    <p style="line-height: 1.6; margin-bottom: 20px;">Lembaga Sertifikasi Produk Balai Besar Pengujian Standar Instrumen Sumber Daya Lahan Pertanian. Melayani sertifikasi produk SNI dengan profesional, independen, dan terpercaya.</p>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-3 col-md-6">
                    <h5 style="color: #fff; font-weight: 700; margin-bottom: 20px; font-size: 16px;">Tautan Cepat</h5>
                    <ul class="list-unstyled footer-links" style="padding: 0; margin: 0; line-height: 2;">
                        <li><a href="#beranda" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Beranda</a></li>
                        <li><a href="#ruang-lingkup" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Ruang Lingkup</a></li>
                        <li><a href="#alur-sertifikasi" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Alur Sertifikasi</a></li>
                        <li><a href="#alur-survailen" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Alur Survailen</a></li>
                        @guest
                            <li><a href="{{ route('login') }}" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Login Portal</a></li>
                        @endguest
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-5 col-md-12">
                    <h5 style="color: #fff; font-weight: 700; margin-bottom: 20px; font-size: 16px;">Hubungi Kami</h5>
                    <div class="d-flex mb-3">
                        <i class="fa-solid fa-location-dot mt-1 me-3" style="color: #16a34a; font-size: 18px; width: 20px; text-align: center;"></i>
                        <span style="line-height: 1.6;">Jl. Tentara Pelajar No.12, Ciwaringin, Bogor Tengah, Kota Bogor, Jawa Barat 16114</span>
                    </div>
                    <div class="d-flex mb-3 align-items-center">
                        <i class="fa-solid fa-envelope me-3" style="color: #16a34a; font-size: 16px; width: 20px; text-align: center;"></i>
                        <span>lspro.brmpsdlp@pertanian.go.id</span>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="d-flex mb-3 align-items-center">
                                <i class="fa-solid fa-phone me-3" style="color: #16a34a; font-size: 16px; width: 20px; text-align: center;"></i>
                                <span>+62 812-3456-7890</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex mb-3 align-items-center">
                                <i class="fa-solid fa-fax me-3" style="color: #16a34a; font-size: 16px; width: 20px; text-align: center;"></i>
                                <span>(0251) 8321608</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-4 mt-4 text-center border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                <p class="mb-0" style="font-size: 13px; color: #64748b;">&copy; {{ date('Y') }} Balai Besar Pengujian Standar Instrumen Sumber Daya Lahan Pertanian. Semua Hak Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Navbar ScrollSpy Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('#landing-nav-menu .nav-link');

            window.addEventListener('scroll', () => {
                let current = '';
                
                // Jika scroll di paling atas, set aktif ke beranda
                if (window.scrollY < 100) {
                    current = 'beranda';
                } else {
                    sections.forEach(section => {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.clientHeight;
                        if (scrollY >= (sectionTop - 150)) {
                            current = section.getAttribute('id');
                        }
                    });
                }

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
