import React, { useEffect, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';

export default function Welcome({ auth }) {
    const [activeSection, setActiveSection] = useState('beranda');
    const handleLoginClick = (e) => {
        e.preventDefault();
        document.body.classList.remove('loaded'); // Tampilkan loading Kementan
        setTimeout(() => {
            window.location.href = '/login';
        }, 800); // Tunda 800ms agar efek loading Kementan terlihat
    };

    useEffect(() => {
        const handleScroll = () => {
            const sections = document.querySelectorAll('section[id]');
            let current = '';
            if (window.scrollY < 100) {
                current = 'beranda';
            } else {
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (window.scrollY >= (sectionTop - 150)) {
                        current = section.getAttribute('id');
                    }
                });
            }
            setActiveSection(current);
        };

        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const role = auth?.user?.role ? auth.user.role.toLowerCase().trim() : '';
    const dashboardRoute = (role === 'admin' || role === 'superadmin') ? '/admin/dashboard' : '/dashboard';

    return (
        <>
            <Head title="LSPro BRMP SDLP - Kementerian Pertanian" />
            <style>{`
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

                /* Timeline Steps (for Survailen & Sertifikasi) */
                .alur-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
                .steps-flow { display: flex; justify-content: space-between; padding-bottom: 4px; overflow-x: auto; }
                .step-item  { display: flex; flex-direction: column; align-items: center; text-align: center; flex: 1 1 0px; position: relative; min-width: 140px; }
                .step-row   { display: flex; justify-content: center; width: 100%; position: relative; margin-bottom: 16px; }
                .step-circle { width: 44px; height: 44px; border-radius: 50%; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3); position: relative; z-index: 2; }
                .step-circle.blue { background: #2563eb; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3); }
                .step-line  { position: absolute; top: 50%; left: 50%; width: 100%; height: 3px; background: #d1fae5; transform: translateY(-50%); z-index: 1; }
                .step-line.blue { background: #bfdbfe; }
                .step-label { font-size: 13px; font-weight: 600; color: #374151; line-height: 1.5; padding: 0 10px; width: 100%; word-wrap: break-word; white-space: normal; }

                /* ===== HERO SECTION ===== */
                .hero {
                    position: relative;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    background-image: url('/assets/bg-login-reg.jpg');
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
            `}</style>

            <nav className="landing-navbar">
                <div className="container d-flex justify-content-between align-items-center">
                    <a href="#" className="d-flex align-items-center gap-3 text-decoration-none">
                        <img src="/assets/kementan.png" alt="Logo Kementan" style={{ height: '42px', width: 'auto', objectFit: 'contain' }} />
                        <div>
                            <div className="nav-logo-text">Kementerian Pertanian RI</div>
                            <div className="nav-logo-title">Layanan Sertifikasi Produk BBPM SDLP</div>
                        </div>
                    </a>

                    <ul className="nav d-none d-xl-flex align-items-center" id="landing-nav-menu">
                        <li className="nav-item"><a href="#beranda" className={`nav-link ${activeSection === 'beranda' ? 'active' : ''}`}>Beranda</a></li>
                        <li className="nav-item"><a href="#tentang-lspro" className={`nav-link ${activeSection === 'tentang-lspro' ? 'active' : ''}`}>Tentang LSPro</a></li>
                        <li className="nav-item"><a href="#visi-misi" className={`nav-link ${activeSection === 'visi-misi' ? 'active' : ''}`}>Visi dan Misi</a></li>
                        <li className="nav-item"><a href="#ruang-lingkup" className={`nav-link ${activeSection === 'ruang-lingkup' ? 'active' : ''}`}>Ruang Lingkup</a></li>
                        <li className="nav-item"><a href="#alur-sertifikasi" className={`nav-link ${activeSection === 'alur-sertifikasi' ? 'active' : ''}`}>Alur Sertifikasi</a></li>
                    </ul>

                    <div>
                        {auth.user ? (
                            <>
                                <a href={dashboardRoute} className="btn-masuk">Dashboard</a>
                            </>
                        ) : (
                            <a 
                                href="/login" 
                                className="btn-masuk" 
                                onClick={handleLoginClick}
                            >
                                <span>Masuk</span>
                            </a>
                        )}
                    </div>
                </div>
            </nav>

            <section className="hero" id="beranda">
                <div className="container">
                    <div className="hero-content">
                        <div className="hero-badge">
                            <i className="fa-solid fa-shield-check"></i>
                            Terakreditasi SNI ISO/IEC 17065:2012
                        </div>
                        <h1>Layanan Sertifikasi Produk<br />BBPM SDLP</h1>
                        <p>Balai Besar Perakitan dan Modernisasi Sumber Daya Lahan Pertanian melayani jasa sertifikasi pupuk secara mandiri, profesional, tidak diskriminatif, menjaga kerahasiaan pelanggan, serta menjamin hasil sertifikasi yang didukung oleh personel yang kompeten dan profesional.</p>

                        <div className="d-flex gap-3 flex-wrap">
                            <motion.div whileHover={{ scale: 1.05 }}>
                                {auth?.user ? (
                                    <a href="/pengajuan/sertifikasi" className="btn-hero-primary">
                                        Ajukan Sertifikasi <i className="fa-solid fa-arrow-right"></i>
                                    </a>
                                ) : (
                                    <a 
                                        href="/login" 
                                        className="btn-hero-primary"
                                        onClick={handleLoginClick}
                                    >
                                        <>Ajukan Sertifikasi <i className="fa-solid fa-arrow-right"></i></>
                                    </a>
                                )}
                            </motion.div>
                            <motion.div whileHover={{ scale: 1.05 }}>
                                <a href="#tentang-lspro" className="btn-hero-outline">
                                    Pelajari LSPro
                                </a>
                            </motion.div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="tentang-lspro" className="content-section">
                <div className="container">
                    <h2 className="section-title">Kenali Kami Lebih Dekat</h2>
                    <p className="section-subtitle">Lembaga sertifikasi terpercaya untuk memajukan kualitas produk pertanian Indonesia.</p>

                    <div className="row g-4">
                        <div className="col-md-12">
                            <motion.div whileHover={{ scale: 1.05, y: -5 }} className="info-card accent-blue">
                                <div className="card-icon icon-blue">
                                    <i className="fa-solid fa-certificate"></i>
                                </div>
                                <h3>Apa itu LSPro?</h3>
                                <p>Lembaga Sertifikasi Produk (LSPro) BRMP SDLP adalah unit sertifikasi yang bertugas menilai kesesuaian produk pupuk dan pembenah tanah terhadap Standar Nasional Indonesia (SNI).</p>
                                <p>LSPro BRMP SDLP beroperasi sesuai SNI ISO/IEC 17065:2012 dan telah terakreditasi oleh Komite Akreditasi Nasional (KAN). Kami menjamin independensi, kerahasiaan, dan objektivitas dalam setiap proses sertifikasi.</p>
                            </motion.div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="visi-misi" className="content-section bg-white">
                <div className="container">
                    <h2 className="section-title">Visi & Misi</h2>
                    <p className="section-subtitle">Arah dan tujuan utama dari pelayanan kami.</p>

                    <motion.div whileHover={{ scale: 1.02 }} className="info-card accent-purple">
                        <div className="row g-5">
                            <div className="col-md-6">
                                <div className="d-flex gap-3">
                                    <div className="card-icon icon-purple" style={{ flexShrink: 0 }}>
                                        <i className="fa-solid fa-eye"></i>
                                    </div>
                                    <div>
                                        <h4 style={{ fontWeight: 800, fontSize: '18px', marginBottom: '12px', color: '#1e293b' }}>Visi</h4>
                                        <p style={{ fontSize: '15px', color: '#475569', lineHeight: 1.7 }}>Menjadi lembaga sertifikasi produk pupuk dan pembenah tanah yang profesional, independen, dan terpercaya di tingkat nasional maupun internasional untuk mendukung pertanian yang berkelanjutan.</p>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6">
                                <div className="d-flex gap-3">
                                    <div className="card-icon icon-purple" style={{ flexShrink: 0 }}>
                                        <i className="fa-solid fa-rocket"></i>
                                    </div>
                                    <div>
                                        <h4 style={{ fontWeight: 800, fontSize: '18px', marginBottom: '12px', color: '#1e293b' }}>Misi</h4>
                                        <ul style={{ fontSize: '15px', color: '#475569', lineHeight: 1.7, paddingLeft: '20px' }}>
                                            <li>Menyelenggarakan proses sertifikasi yang objektif, transparan, dan tidak memihak.</li>
                                            <li>Meningkatkan kompetensi personel secara berkesinambungan.</li>
                                            <li>Memberikan pelayanan prima demi tercapainya kepuasan pelanggan.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </section>

            <section id="ruang-lingkup" className="content-section">
                <div className="container">
                    <h2 className="section-title">Daftar 10 Ruang Lingkup</h2>
                    <p className="section-subtitle">Ruang lingkup sertifikasi kesesuaian produk pupuk untuk SNI wajib.</p>
                    <div className="row mt-4 justify-content-center">
                        <div className="col-md-8">
                            <ul style={{ listStyleType: 'decimal', lineHeight: '2', fontSize: '15px', color: '#475569', fontWeight: '600' }}>
                                <li>Pupuk amonium sulfat (ZA) - SNI 02-1760-2005</li>
                                <li>Pupuk kalium klorida (KCl) - SNI 02-2805-2005</li>
                                <li>Pupuk SP-36 - SNI 02-3769-2005</li>
                                <li>Pupuk fosfat alam untuk pertanian - SNI 02-3776-2005</li>
                                <li>Pupuk urea - SNI 2801:2010</li>
                                <li>Pupuk NPK Padat - SNI 2803:2024</li>
                                <li>Kapur untuk pertanian - SNI 02-0482:1998</li>
                                <li>Pupuk organik padat - SNI 7763:2018 dan SNI 7763:2024</li>
                                <li>Pupuk dolomit - SNI 02-2804-2005</li>
                                <li>Pupuk Kiserit - SNI 02-2807-1992</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section id="alur-sertifikasi" className="content-section bg-white">
                <div className="container text-center">
                    <h2 className="section-title">Alur Sertifikasi Tipe 5</h2>
                    <p className="section-subtitle">Proses sertifikasi produk SNI di LSPro BRMP SDLP yang transparan dan terukur.</p>

                    <div className="alur-box max-w-4xl mx-auto" style={{ maxWidth: '1100px', margin: '0 auto' }}>
                        <div className="steps-flow mt-3 mb-2" style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: '20px' }}>
                            {['Pengajuan dari Pemohon', 'Penerimaan & Pemeriksaan Berkas', 'Penerbitan Invoice / Tagihan', 'Pembuatan Perjanjian Sertifikasi', 'Penugasan Tim Auditor & PPC', 'Audit Kecukupan Data Sampel', 'Perencanaan Audit & Pengambilan Sampel', 'Audit Kesesuaian', 'Evaluasi Hasil Audit', 'Penerbitan Sertifikat'].map((step, i) => (
                                <motion.div whileHover={{ scale: 1.1 }} className="step-item" style={{ width: '130px' }} key={i}>
                                    <div className="step-row" style={{ display: 'flex', justifyContent: 'center', marginBottom: '10px' }}>
                                        <div className="step-circle" style={{ backgroundColor: '#16a34a', color: 'white', width: '40px', height: '40px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 'bold' }}>{i + 1}</div>
                                    </div>
                                    <span className="step-label" style={{ fontSize: '12px', fontWeight: 600 }}>{step}</span>
                                </motion.div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section id="alur-sertifikasi-1b" className="content-section bg-light">
                <div className="container text-center">
                    <h2 className="section-title">Alur Sertifikasi Tipe 1b</h2>
                    <p className="section-subtitle">Proses sertifikasi Sertifikat Kesesuaian SNI untuk skema sertifikasi Tipe 1b.</p>

                    <div className="alur-box max-w-4xl mx-auto" style={{ maxWidth: '1100px', margin: '0 auto' }}>
                        <div className="steps-flow mt-3 mb-2" style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: '20px' }}>
                            {['Pengajuan dari Pemohon', 'Penerimaan & Pemeriksaan Berkas', 'Penerbitan Invoice / Tagihan', 'Pembuatan Perjanjian Sertifikasi', 'Penugasan Tim Pengambil Contoh', 'Perencanaan Audit & Pengambilan Sampel', 'Pelaksanaan Audit', 'Evaluasi Hasil Audit', 'Verifikasi Tindak Lanjut Perbaikan', 'Penerbitan Sertifikat'].map((step, i) => (
                                <motion.div whileHover={{ scale: 1.1 }} className="step-item" style={{ width: '130px' }} key={i}>
                                    <div className="step-row" style={{ display: 'flex', justifyContent: 'center', marginBottom: '10px' }}>
                                        <div className="step-circle" style={{ backgroundColor: '#f59e0b', color: 'white', width: '40px', height: '40px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 'bold' }}>{i + 1}</div>
                                    </div>
                                    <span className="step-label" style={{ fontSize: '12px', fontWeight: 600 }}>{step}</span>
                                </motion.div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <footer id="kontak" style={{ backgroundColor: '#0f172a', color: '#94a3b8', padding: '60px 0 20px 0', fontSize: '14px' }}>
                <div className="container">
                    <div className="row gy-4 mb-5">
                        <div className="col-lg-4 col-md-6 pe-lg-5">
                            <div className="logo d-flex align-items-center mb-3">
                                <img src="/assets/kementan.png" alt="Logo Kementan" style={{ height: '40px', width: 'auto', marginRight: '12px', filter: 'drop-shadow(0 0 2px rgba(255,255,255,0.8))' }} />
                                <span style={{ fontWeight: 800, fontSize: '18px', color: '#fff' }}>Layanan Sertifikasi Produk</span>
                            </div>
                            <p style={{ lineHeight: 1.6, marginBottom: '20px' }}>Balai Besar Perakitan dan Modernisasi Sumber Daya Lahan Pertanian melayani jasa sertifikasi pupuk secara mandiri, profesional, tidak diskriminatif, menjaga kerahasiaan pelanggan, serta menjamin hasil sertifikasi yang didukung oleh personel yang kompeten dan profesional.</p>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <h5 style={{ color: '#fff', fontWeight: 700, marginBottom: '20px', fontSize: '16px' }}>Tautan Cepat</h5>
                            <ul className="list-unstyled footer-links" style={{ padding: 0, margin: 0, lineHeight: 2 }}>
                                <li><a href="#beranda" style={{ color: '#cbd5e1', textDecoration: 'none', transition: '0.3s' }}><i className="fa-solid fa-chevron-right me-2" style={{ fontSize: '10px', color: '#16a34a' }}></i>Beranda</a></li>
                                <li><a href="#ruang-lingkup" style={{ color: '#cbd5e1', textDecoration: 'none', transition: '0.3s' }}><i className="fa-solid fa-chevron-right me-2" style={{ fontSize: '10px', color: '#16a34a' }}></i>Ruang Lingkup</a></li>
                                <li><a href="#alur-sertifikasi" style={{ color: '#cbd5e1', textDecoration: 'none', transition: '0.3s' }}><i className="fa-solid fa-chevron-right me-2" style={{ fontSize: '10px', color: '#16a34a' }}></i>Alur Sertifikasi</a></li>
                                <li><a href="#alur-sertifikasi-1b" style={{ color: '#cbd5e1', textDecoration: 'none', transition: '0.3s' }}><i className="fa-solid fa-chevron-right me-2" style={{ fontSize: '10px', color: '#16a34a' }}></i>Alur Sertifikasi Tipe 1b</a></li>
                                {!auth?.user && (
                                    <li><a href="/login" style={{ color: '#cbd5e1', textDecoration: 'none', transition: '0.3s' }} onClick={handleLoginClick}><i className="fa-solid fa-chevron-right me-2" style={{ fontSize: '10px', color: '#16a34a' }}></i>Login Portal</a></li>
                                )}
                            </ul>
                        </div>

                        <div className="col-lg-5 col-md-12">
                            <h5 style={{ color: '#fff', fontWeight: 700, marginBottom: '20px', fontSize: '16px' }}>Hubungi Kami</h5>
                            <div className="d-flex mb-3">
                                <i className="fa-solid fa-location-dot mt-1 me-3" style={{ color: '#16a34a', fontSize: '18px', width: '20px', textAlign: 'center' }}></i>
                                <span style={{ lineHeight: 1.6 }}>Jl. Tentara Pelajar No.12, Ciwaringin, Bogor Tengah, Kota Bogor, Jawa Barat 16114</span>
                            </div>
                            <div className="d-flex mb-3 align-items-center">
                                <i className="fa-solid fa-envelope me-3" style={{ color: '#16a34a', fontSize: '16px', width: '20px', textAlign: 'center' }}></i>
                                <span>lspro.brmpsdlp@pertanian.go.id</span>
                            </div>
                            <div className="row">
                                <div className="col-sm-6">
                                    <div className="d-flex mb-3 align-items-center">
                                        <i className="fa-solid fa-phone me-3" style={{ color: '#16a34a', fontSize: '16px', width: '20px', textAlign: 'center' }}></i>
                                        <span>+62 812-3456-7890</span>
                                    </div>
                                </div>
                                <div className="col-sm-6">
                                    <div className="d-flex mb-3 align-items-center">
                                        <i className="fa-solid fa-fax me-3" style={{ color: '#16a34a', fontSize: '16px', width: '20px', textAlign: 'center' }}></i>
                                        <span>(0251) 8321608</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="pt-4 mt-4 text-center border-top" style={{ borderColor: 'rgba(255,255,255,0.1) !important' }}>
                        <p className="mb-0" style={{ fontSize: '13px', color: '#64748b' }}>&copy; {new Date().getFullYear()} Balai Besar Pengujian Standar Instrumen Sumber Daya Lahan Pertanian. Semua Hak Dilindungi.</p>
                    </div>
                </div>
            </footer>
        </>
    );
}
