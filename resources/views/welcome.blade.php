<!DOCTYPE html>
<html lang="id" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Sertifikasi Produk BBPM SDLP - Kementerian Pertanian</title>
    
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
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.95) 0%, rgba(16, 185, 129, 0.8) 50%, rgba(4, 120, 87, 0.95) 100%);
            background-size: 200% 200%;
            animation: gradientMove 10s ease infinite;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            padding: 40px 0;
        }
        /* Floating shapes */
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
            z-index: 1;
        }
        .shape-1 { width: 100px; height: 100px; top: 15%; left: 10%; animation-delay: 0s; }
        .shape-2 { width: 150px; height: 150px; top: 40%; right: 10%; animation-delay: 2s; border-radius: 20%; transform: rotate(45deg); }
        .shape-3 { width: 80px; height: 80px; bottom: 20%; left: 20%; animation-delay: 4s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
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
            transition: all 0.3s ease;
        }
        
        .alur-step-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
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
        
        /* Custom Animations */
        .btn-hero-primary {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(34, 197, 94, 0.25);
        }
    </style>
    
    <!-- AOS CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
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
                    <div class="nav-logo-title">Layanan Sertifikasi Produk BBPM SDLP</div>
                </div>
            </a>

            <!-- Menu (Desktop) -->
            <ul class="nav d-none d-xl-flex align-items-center" id="landing-nav-menu">
                <li class="nav-item"><a href="#beranda" class="nav-link active">Beranda</a></li>
                <li class="nav-item"><a href="#tentang-lspro" class="nav-link">Tentang LSPro</a></li>
                <li class="nav-item"><a href="#visi-misi" class="nav-link">Visi dan Misi</a></li>
                <li class="nav-item"><a href="#ruang-lingkup" class="nav-link">Ruang Lingkup</a></li>
                <li class="nav-item"><a href="#alur-sertifikasi" class="nav-link">Alur Sertifikasi</a></li>
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
        <!-- Floating Shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        
        <div class="container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <div class="hero-badge" data-aos="fade-down" data-aos-delay="200">
                    <i class="fa-solid fa-shield-check"></i>
                    Terakreditasi SNI ISO/IEC 17065:2012
                </div>
                <h1>Layanan Sertifikasi Produk<br>BBPM SDLP</h1>
                <p>Layanan ini merupakan Sertifikasi Produk di bawah BBPM SDLP yang melayani jasa sertifikasi pupuk secara mandiri, profesional, tidak diskriminatif, menjaga kerahasiaan pelanggan, serta menjamin hasil sertifikasi yang didukung oleh personel yang kompeten dan profesional.</p>
                
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
                    <a href="https://brmp.pertanian.go.id" class="btn-hero-outline" target="_blank">
                        <i class="fa-solid fa-link"></i> Website BRMP SDLP
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

            <div class="row g-4 justify-content-center">
                <div class="col-md-8">
                    <div class="info-card accent-blue text-center" data-aos="fade-up" data-aos-duration="800">
                        <div class="card-icon icon-blue mx-auto">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h3>Layanan Sertifikasi Produk (LSPro)</h3>
                        <p>Layanan Sertifikasi Produk (LSPro) di bawah BBPM SDLP adalah unit sertifikasi yang bertugas menilai kesesuaian produk pupuk dan pembenah tanah terhadap Standar Nasional Indonesia (SNI).</p>
                        <p class="mt-2">Layanan kami beroperasi sesuai SNI ISO/IEC 17065:2012 dan telah terakreditasi oleh Komite Akreditasi Nasional (KAN). Kami menjamin independensi, kerahasiaan, dan objektivitas dalam setiap proses sertifikasi Sertifikat Kesesuaian SNI.</p>
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

            <div class="info-card accent-purple" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
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
                    <div class="info-card text-center" style="padding: 24px;" data-aos="fade-up" data-aos-delay="100">
                        <i class="fa-solid fa-leaf mb-3" style="color: #16a34a; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk Organik</h4>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;" data-aos="fade-up" data-aos-delay="200">
                        <i class="fa-solid fa-flask mb-3" style="color: #2563eb; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk NPK</h4>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;" data-aos="fade-up" data-aos-delay="300">
                        <i class="fa-solid fa-vial mb-3" style="color: #f59e0b; font-size: 32px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin:0;">Pupuk Urea</h4>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card text-center" style="padding: 24px;" data-aos="fade-up" data-aos-delay="400">
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
            <h2 class="section-title" data-aos="fade-up">Alur Sertifikasi Tipe 5</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Proses sertifikasi produk SNI di LSPro BRMP SDLP yang transparan dan terukur.</p>
            
            <div class="alur-box max-w-4xl mx-auto" style="max-width: 1000px; margin: 0 auto;" data-aos="fade-up" data-aos-delay="200">
                <div class="steps-flow mt-3 mb-2">
                    @php
                    $stepsSertifikasi = [
                        'Pengajuan Permohonan', 
                        'Tinjauan Permohonan & Dokumen', 
                        'Evaluasi (Audit & Pengujian)', 
                        'Tinjauan Hasil Evaluasi', 
                        'Keputusan Sertifikasi',
                        'Penerbitan Sertifikat Kesesuaian SNI'
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

    <!-- ALUR SERTIFIKASI TIPE 1b -->
    <section class="content-section bg-white">
        <div class="container text-center">
            <h2 class="section-title" data-aos="fade-up">Alur Sertifikasi Tipe 1b</h2>
            <p class="section-subtitle mb-2" data-aos="fade-up" data-aos-delay="100">Proses sertifikasi Sertifikat Kesesuaian SNI untuk skema sertifikasi Tipe 1b.</p>
            <p class="text-muted" style="max-width: 800px; margin: 0 auto 40px auto; font-size: 14px;" data-aos="fade-up" data-aos-delay="150">
                *Sertifikasi Tipe 1b (Sistem Batch/Lot) diperuntukkan bagi pengajuan sertifikasi untuk produk dalam satu kali pengiriman atau batch produksi tertentu. Setiap lot yang diajukan akan melalui proses sampling dan pengujian laboratorium yang independen guna menjamin kesesuaian dengan standar SNI.
            </p>
            
            <div class="alur-box max-w-4xl mx-auto mt-4" style="max-width: 1100px; margin: 0 auto;" data-aos="fade-up" data-aos-delay="200">
                <div class="steps-flow" style="overflow-x: auto; padding-bottom: 10px;">
                    @php
                    $stepsTipe1b = [
                        'Pengajuan dari Pemohon', 
                        'Penerimaan & Pemeriksaan Berkas', 
                        'Pembuatan Perjanjian Sertifikasi', 
                        'Penerbitan Invoice / Tagihan',
                        'Penugasan Tim Pengambil Contoh',
                        'Perencanaan Audit & Pengambilan Sampel',
                        'Pelaksanaan Audit',
                        'Evaluasi Hasil Audit',
                        'Penerbitan Sertifikat',
                        'Penyerahan Sertifikat'
                    ];
                    @endphp
                    @foreach($stepsTipe1b as $i => $s)
                    <div class="step-item" style="min-width: 140px; margin-bottom: 10px;">
                        <div class="step-row mb-2 d-flex justify-content-center">
                            <div class="step-circle" style="background: #f59e0b; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <span class="step-label" style="font-size: 12px; font-weight: 600; color: #374151;">{{ $s }}</span>
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
                        <span style="font-weight: 800; font-size: 18px; color: #fff;">Layanan Sertifikasi Produk</span>
                    </div>
                    <p style="line-height: 1.6; margin-bottom: 20px;">Layanan ini merupakan Sertifikasi Produk di bawah BBPM SDLP yang melayani jasa sertifikasi pupuk secara mandiri, profesional, tidak diskriminatif, menjaga kerahasiaan pelanggan, serta menjamin hasil sertifikasi yang didukung oleh personel yang kompeten dan profesional.</p>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-3 col-md-6">
                    <h5 style="color: #fff; font-weight: 700; margin-bottom: 20px; font-size: 16px;">Tautan Cepat</h5>
                    <ul class="list-unstyled footer-links" style="padding: 0; margin: 0; line-height: 2;">
                        <li><a href="#beranda" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Beranda</a></li>
                        <li><a href="#ruang-lingkup" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Ruang Lingkup</a></li>
                        <li><a href="#alur-sertifikasi" style="color: #cbd5e1; text-decoration: none; transition: 0.3s;"><i class="fa-solid fa-chevron-right me-2" style="font-size: 10px; color: #16a34a;"></i>Alur Sertifikasi</a></li>
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
                <p class="mb-0" style="font-size: 13px; color: #64748b;">&copy; {{ date('Y') }} BBPM SDLP Kementerian Pertanian. Semua Hak Dilindungi.</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1200,
            easing: 'ease-out-quint',
            once: true,
            offset: 100
        });
    </script>
</body>
</html>
