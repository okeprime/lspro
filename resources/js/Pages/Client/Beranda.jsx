import React, { useState, useEffect } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { motion, AnimatePresence } from 'framer-motion';

export default function Beranda({ auth, totalPengajuan, pengajuanAktif, sertifikatTerbit, menungguPembayaran, recentPengajuan }) {
    const { flash } = usePage().props;
    const today = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    
    // Check if user just registered (has success flash message)
    const [showOnboarding, setShowOnboarding] = useState(false);
    
    useEffect(() => {
        if (flash?.success && flash.success.toLowerCase().includes('registrasi')) {
            setShowOnboarding(true);
        }
    }, [flash]);

    return (
        <AuthenticatedLayout user={auth.user} header="Dashboard Client">
            <Head title="Dashboard" />
            <style>{`
                /* ===== DASHBOARD HERO ===== */
                .dashboard-hero {
                    background: linear-gradient(135deg, #022c22 0%, #064e3b 100%);
                    border-radius: 16px;
                    padding: 30px;
                    color: white;
                    position: relative;
                    overflow: hidden;
                    margin-bottom: 24px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                }
                .dashboard-hero::before {
                    content: ''; position: absolute;
                    top: -50px; right: -50px;
                    width: 200px; height: 200px;
                    background: rgba(22, 163, 74, 0.15);
                    border-radius: 50%;
                }
                .hero-content { position: relative; z-index: 1; }
                .badge-status {
                    display: inline-flex; align-items: center; gap: 6px;
                    background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3);
                    color: #6ee7b7; border-radius: 99px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;
                    margin-bottom: 15px;
                }

                /* ===== STATS CARDS ===== */
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                    gap: 20px;
                    margin-bottom: 24px;
                }
                .stat-card {
                    background: #ffffff;
                    border-radius: 16px;
                    padding: 24px;
                    color: #1e293b;
                    border: 1px solid #e2e8f0;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
                    transition: transform 0.2s, box-shadow 0.2s;
                    display: flex; flex-direction: column; justify-content: space-between;
                }
                .stat-card .stat-icon {
                    width: 44px; height: 44px; border-radius: 12px;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 20px; margin-bottom: 16px;
                }
                .stat-card .stat-value {
                    font-size: 28px; font-weight: 800; margin-bottom: 4px; color: #0f172a;
                }
                .stat-card .stat-label {
                    font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
                }

                /* Specific Colors */
                .card-total { border-top: 4px solid #38bdf8; }
                .card-total .stat-icon { background: #f0f9ff; color: #0ea5e9; }
                .card-total .stat-value { color: #0284c7; }

                .card-proses { border-top: 4px solid #7dd3fc; }
                .card-proses .stat-icon { background: #f0f9ff; color: #38bdf8; }
                .card-proses .stat-value { color: #0369a1; }

                .card-bayar { border-top: 4px solid #22c55e; }
                .card-bayar .stat-icon { background: #f0fdf4; color: #22c55e; }
                .card-bayar .stat-value { color: #15803d; }

                .card-sertifikat { border-top: 4px solid #16a34a; }
                .card-sertifikat .stat-icon { background: #dcfce7; color: #16a34a; }
                .card-sertifikat .stat-value { color: #166534; }

                /* ===== RECENT SECTION ===== */
                .recent-section {
                    background: white;
                    border-radius: 16px;
                    border: 1px solid #e2e8f0;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
                    overflow: hidden;
                }
                .recent-header {
                    padding: 20px 24px;
                    border-bottom: 1px solid #e2e8f0;
                    display: flex; justify-content: space-between; align-items: center;
                }
                .recent-title { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; }
                
                /* ===== ONBOARDING MODAL ===== */
                .onboarding-overlay {
                    position: fixed;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(15, 23, 42, 0.7);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                    backdrop-filter: blur(4px);
                }
                .onboarding-modal {
                    background: white;
                    border-radius: 20px;
                    width: 100%;
                    max-width: 600px;
                    overflow: hidden;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                }
                .onboarding-header {
                    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
                    padding: 30px;
                    color: white;
                    text-align: center;
                }
                .onboarding-body {
                    padding: 30px;
                }
                .info-item {
                    display: flex;
                    gap: 16px;
                    margin-bottom: 20px;
                    align-items: flex-start;
                }
                .info-icon {
                    width: 40px; height: 40px;
                    background: #f0fdf4; color: #16a34a;
                    border-radius: 10px;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 18px; flex-shrink: 0;
                }
                .btn-tutup {
                    width: 100%;
                    padding: 14px;
                    background: #16a34a; color: white;
                    border: none; border-radius: 12px;
                    font-weight: 600; font-size: 15px;
                    cursor: pointer; transition: 0.2s;
                }
                .btn-tutup:hover { background: #15803d; }
            `}</style>

            <AnimatePresence>
                {showOnboarding && (
                    <motion.div 
                        className="onboarding-overlay"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                    >
                        <motion.div 
                            className="onboarding-modal"
                            initial={{ scale: 0.9, y: 20, opacity: 0 }}
                            animate={{ scale: 1, y: 0, opacity: 1 }}
                            exit={{ scale: 0.9, y: 20, opacity: 0 }}
                            transition={{ type: 'spring', damping: 25, stiffness: 300 }}
                        >
                            <div className="onboarding-header">
                                <i className="fa-solid fa-circle-check" style={{ fontSize: '48px', marginBottom: '15px' }}></i>
                                <h2 style={{ margin: 0, fontSize: '24px', fontWeight: 800 }}>Registrasi Berhasil!</h2>
                                <p style={{ margin: '10px 0 0', opacity: 0.9, fontSize: '15px' }}>Selamat datang di Portal Layanan LSPro BRMP SDLP</p>
                            </div>
                            <div className="onboarding-body">
                                <h4 style={{ fontSize: '16px', fontWeight: 700, marginBottom: '20px', color: '#1e293b' }}>Panduan Singkat Penggunaan Dashboard:</h4>
                                
                                <div className="info-item">
                                    <div className="info-icon"><i className="fa-solid fa-file-circle-plus"></i></div>
                                    <div>
                                        <h5 style={{ margin: '0 0 5px', fontSize: '15px', fontWeight: 700, color: '#334155' }}>Tab Pengajuan</h5>
                                        <p style={{ margin: 0, fontSize: '13.5px', color: '#64748b', lineHeight: 1.5 }}>Gunakan menu ini untuk memulai permohonan baru. Anda harus mengisi data perusahaan, data produk pupuk, dan mengunggah dokumen legalitas.</p>
                                    </div>
                                </div>
                                
                                <div className="info-item">
                                    <div className="info-icon" style={{ background: '#f0f9ff', color: '#0ea5e9' }}><i className="fa-solid fa-list-check"></i></div>
                                    <div>
                                        <h5 style={{ margin: '0 0 5px', fontSize: '15px', fontWeight: 700, color: '#334155' }}>Tab Aktivitas</h5>
                                        <p style={{ margin: 0, fontSize: '13.5px', color: '#64748b', lineHeight: 1.5 }}>Di sini Anda dapat memantau status pengajuan yang sedang diproses. Anda dapat melihat tahapan mana yang sedang berjalan (seperti Verifikasi TU atau Audit).</p>
                                    </div>
                                </div>
                                
                                <div className="info-item">
                                    <div className="info-icon" style={{ background: '#fef2f2', color: '#ef4444' }}><i className="fa-solid fa-file-invoice-dollar"></i></div>
                                    <div>
                                        <h5 style={{ margin: '0 0 5px', fontSize: '15px', fontWeight: 700, color: '#334155' }}>Tab Billing</h5>
                                        <p style={{ margin: 0, fontSize: '13.5px', color: '#64748b', lineHeight: 1.5 }}>Jika pengajuan Anda disetujui, tagihan resmi akan muncul di menu Billing. Segera lakukan pembayaran agar proses sertifikasi dapat dilanjutkan.</p>
                                    </div>
                                </div>

                                <div style={{ marginTop: '30px' }}>
                                    <button className="btn-tutup" onClick={() => setShowOnboarding(false)}>
                                        Saya Mengerti, Mulai Gunakan Dashboard
                                    </button>
                                </div>
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>

            <div className="container-fluid py-4" style={{ maxWidth: '1200px', margin: '0 auto' }}>
                
                <div className="dashboard-hero">
                    <div className="hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <div className="badge-status">
                                <i className="fa-solid fa-circle" style={{ fontSize: '8px' }}></i> Akun Terhubung
                            </div>
                            <h1 style={{ fontSize: '24px', fontWeight: 800, marginBottom: '5px' }}>Selamat Datang, {auth.user.nama_perusahaan || auth.user.name}</h1>
                            <p style={{ color: '#cbd5e1', fontSize: '14px', margin: 0 }}>Pusat Monitoring Sertifikasi Produk SNI Anda</p>
                        </div>
                        <div style={{ textAlign: 'right', background: 'rgba(0,0,0,0.2)', padding: '12px 20px', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.1)' }}>
                            <div style={{ fontSize: '11px', color: '#94a3b8', fontWeight: 600, letterSpacing: '0.5px', marginBottom: '4px' }}>TANGGAL HARI INI</div>
                            <div style={{ fontSize: '16px', fontWeight: 700, color: 'white' }}>{today}</div>
                        </div>
                    </div>
                </div>

                <div className="stats-grid">
                    <motion.div whileHover={{ scale: 1.05 }} className="stat-card card-total">
                        <div className="stat-icon"><i className="fa-solid fa-file-lines"></i></div>
                        <div>
                            <div className="stat-value">{totalPengajuan}</div>
                            <div className="stat-label">Total Pengajuan</div>
                        </div>
                    </motion.div>
                    <motion.div whileHover={{ scale: 1.05 }} className="stat-card card-proses">
                        <div className="stat-icon"><i className="fa-solid fa-arrows-rotate"></i></div>
                        <div>
                            <div className="stat-value">{pengajuanAktif}</div>
                            <div className="stat-label">Sedang Diproses</div>
                        </div>
                    </motion.div>
                    <motion.div whileHover={{ scale: 1.05 }} className="stat-card card-bayar">
                        <div className="stat-icon"><i className="fa-solid fa-file-invoice-dollar"></i></div>
                        <div>
                            <div className="stat-value">{menungguPembayaran}</div>
                            <div className="stat-label">Menunggu Pembayaran</div>
                        </div>
                    </motion.div>
                    <motion.div whileHover={{ scale: 1.05 }} className="stat-card card-sertifikat">
                        <div className="stat-icon"><i className="fa-solid fa-award"></i></div>
                        <div>
                            <div className="stat-value">{sertifikatTerbit}</div>
                            <div className="stat-label">Sertifikat Terbit</div>
                        </div>
                    </motion.div>
                </div>

                <motion.div whileHover={{ y: -5 }} className="recent-section">
                    <div className="recent-header">
                        <h2 className="recent-title"><i className="fa-solid fa-clock-rotate-left text-muted me-2"></i> Pengajuan Terbaru</h2>
                        <a href="/aktivitas" className="btn btn-sm btn-outline-success rounded-pill fw-semibold px-3">Lihat Semua</a>
                    </div>
                    <div className="table-responsive">
                        <table className="table table-hover mb-0" style={{ fontSize: '14px' }}>
                            <thead style={{ background: '#f8fafc' }}>
                                <tr>
                                    <th className="py-3 px-4 text-muted fw-semibold" style={{ borderBottom: '1px solid #e2e8f0', fontSize: '12px', textTransform: 'uppercase' }}>ID / Tanggal</th>
                                    <th className="py-3 px-4 text-muted fw-semibold" style={{ borderBottom: '1px solid #e2e8f0', fontSize: '12px', textTransform: 'uppercase' }}>Jenis Sertifikasi</th>
                                    <th className="py-3 px-4 text-muted fw-semibold" style={{ borderBottom: '1px solid #e2e8f0', fontSize: '12px', textTransform: 'uppercase' }}>Status</th>
                                    <th className="py-3 px-4 text-muted fw-semibold text-end" style={{ borderBottom: '1px solid #e2e8f0', fontSize: '12px', textTransform: 'uppercase' }}>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recentPengajuan && recentPengajuan.length > 0 ? (
                                    recentPengajuan.map(item => {
                                        let badgeColor = 'secondary';
                                        if (['diajukan', 'verifikasi_tu'].includes(item.status)) badgeColor = 'primary';
                                        if (item.status === 'selesai') badgeColor = 'success';
                                        if (item.status === 'ditolak') badgeColor = 'danger';
                                        if (item.status === 'menunggu_pembayaran') badgeColor = 'warning text-dark';

                                        return (
                                            <tr key={item.id}>
                                                <td className="py-3 px-4 align-middle">
                                                    <div className="fw-bold text-dark">{item.nomor_registrasi ? '#' + item.nomor_registrasi : '#' + item.id}</div>
                                                    <div className="text-muted" style={{ fontSize: '12px' }}>{new Date(item.created_at).toLocaleDateString('id-ID')}</div>
                                                </td>
                                                <td className="py-3 px-4 align-middle">
                                                    <div className="fw-bold" style={{ color: '#0f172a', textTransform: 'capitalize' }}>{item.jenis_pengajuan}</div>
                                                </td>
                                                <td className="py-3 px-4 align-middle">
                                                    <span className={`badge bg-${badgeColor} rounded-pill`} style={{ fontWeight: 500, padding: '5px 10px', textTransform: 'uppercase' }}>
                                                        {item.status.replace(/_/g, ' ')}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 align-middle text-end">
                                                    {item.status === 'draft' ? (
                                                        <div className="d-flex justify-content-end gap-2">
                                                            <a href={`/pengajuan/form?draft_id=${item.id}`} className="btn btn-sm btn-primary rounded-pill px-3">
                                                                Lanjutkan
                                                            </a>
                                                            <button 
                                                                onClick={() => {
                                                                    if(confirm('Apakah Anda yakin ingin menghapus draft ini?')) {
                                                                        const form = document.createElement('form');
                                                                        form.method = 'POST';
                                                                        form.action = `/pengajuan/draft/${item.id}`;
                                                                        const methodField = document.createElement('input');
                                                                        methodField.type = 'hidden';
                                                                        methodField.name = '_method';
                                                                        methodField.value = 'DELETE';
                                                                        const csrfField = document.createElement('input');
                                                                        csrfField.type = 'hidden';
                                                                        csrfField.name = '_token';
                                                                        csrfField.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                                                        form.appendChild(methodField);
                                                                        form.appendChild(csrfField);
                                                                        document.body.appendChild(form);
                                                                        form.submit();
                                                                    }
                                                                }}
                                                                className="btn btn-sm btn-danger rounded-pill px-3">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    ) : (
                                                        <a href={`/aktivitas/${item.id}/detail`} className="btn btn-sm btn-light border text-primary fw-semibold rounded-pill px-3">
                                                            Detail
                                                        </a>
                                                    )}
                                                </td>
                                            </tr>
                                        )
                                    })
                                ) : (
                                    <tr>
                                        <td colSpan="4" className="text-center py-5 text-muted">
                                            <div style={{ fontSize: '40px', marginBottom: '10px', opacity: 0.3 }}><i className="fa-solid fa-folder-open"></i></div>
                                            Belum ada pengajuan sertifikasi yang dilakukan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </motion.div>
            </div>
        </AuthenticatedLayout>
    );
}
