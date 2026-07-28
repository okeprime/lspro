import React, { useState, useEffect } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';

export default function AuthenticatedLayout({ user, header, children }) {
    const [sidebarActive, setSidebarActive] = useState(false);
    const [pengajuanActive, setPengajuanActive] = useState(false);
    const [aktivitasActive, setAktivitasActive] = useState(false);

    const toggleSidebar = () => {
        const newState = !sidebarActive;
        setSidebarActive(newState);
        if (newState) {
            document.body.classList.add('sidebar-active');
        } else {
            document.body.classList.remove('sidebar-active');
        }
    };

    const role = user?.role ? user.role.toLowerCase() : 'client';
    const subRole = user?.sub_role ? user.sub_role.toLowerCase() : '';
    const isInternal = ['superadmin', 'admin'].includes(role);

    // Apply sidebar-active class to body for desktop
    useEffect(() => {
        if (window.innerWidth >= 992) {
            document.body.classList.add('sidebar-active');
            setSidebarActive(true);
        } else {
            document.body.classList.remove('sidebar-active');
            setSidebarActive(false);
        }

        const handleResize = () => {
            if (window.innerWidth >= 992) {
                document.body.classList.add('sidebar-active');
                setSidebarActive(true);
            } else {
                document.body.classList.remove('sidebar-active');
                setSidebarActive(false);
            }
        };

        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    return (
        <>
            <Head>
                <link rel="stylesheet" href="/css/style.css" />
                <style>{`
                    .dropdown-toggle::after { display: none !important; }
                    .user-profile-top { cursor: pointer; display: flex; align-items: center; background: none; border: none; padding: 0; }
                    .top-navbar { display: flex; justify-content: space-between; align-items: center; }
                    .dropdown-menu-profile { border-radius: 12px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; border: 1px solid #e2e8f0 !important; padding: 8px 0; margin-top: 10px !important; }
                    .nav-link-dropdown { display:flex; justify-content:space-between; align-items:center; width:100%; color:inherit; text-decoration:none; }
                    .dropdown-menu-sidebar { display:none; list-style:none; padding-left:20px; margin-top:8px; }
                    .nav-item-with-dropdown.active .dropdown-menu-sidebar { display:block; }
                    .dropdown-icon { transition:.3s; }
                    .nav-item-with-dropdown.active .dropdown-icon { transform:rotate(180deg); }
                    
                    @media (min-width: 992px) {
                        body { transition: margin-left 0.3s ease; }
                        body.sidebar-active { margin-left: 260px; }
                        .top-navbar { transition: left 0.3s ease; }
                        body.sidebar-active .top-navbar { left: 260px; }
                        body.sidebar-active .overlay { display: none !important; }
                        body.sidebar-active .sidebar { left: 0 !important; }
                    }
                `}</style>
            </Head>

            <aside className={`sidebar ${sidebarActive ? 'active' : ''}`} id="sidebar">
                <div className="brand" style={{ padding: '20px' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', width: '100%' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                            <img src="/assets/kementan.png" alt="Logo" style={{ height: '42px', width: 'auto', maxWidth: 'none', marginBottom: 0 }} />
                            <div style={{ textAlign: 'left', lineHeight: 1.2 }}>
                                <div style={{ fontSize: '11px', fontWeight: 500, color: '#cbd5e1' }}>Kementerian Pertanian RI</div>
                                <div style={{ fontSize: '14px', fontWeight: 800, color: '#4ade80' }}>LSPro BRMP SDLP</div>
                            </div>
                        </div>
                        <i className="fa-solid fa-xmark" id="close-sidebar" onClick={() => setSidebarActive(false)} style={{ cursor: 'pointer', color: '#a7f3d0', fontSize: '20px', marginTop: '10px' }}></i>
                    </div>
                </div>

                <ul className="nav-links">
                    {!isInternal ? (
                        <>
                            <li>
                                <a href="/dashboard" className={window.location.pathname === '/dashboard' ? 'active' : ''}>
                                    <i className="fa-solid fa-gauge"></i> Dashboard
                                </a>
                            </li>

                            <li className={`nav-item-with-dropdown ${pengajuanActive || window.location.pathname.startsWith('/pengajuan') ? 'active' : ''}`}>
                                <a href="#" className="nav-link-dropdown" onClick={(e) => { e.preventDefault(); setPengajuanActive(!pengajuanActive); }}>
                                    <span>
                                        <i className="fa-solid fa-file-circle-plus"></i> Pengajuan
                                    </span>
                                    <i className="fa-solid fa-chevron-down dropdown-icon dropdown-toggle-arrow" style={{ cursor: 'pointer', padding: '5px' }}></i>
                                </a>
                                <ul className="dropdown-menu-sidebar">
                                    <li><a href="/pengajuan/sertifikasi"><i className="fa-solid fa-certificate"></i> Sertifikasi</a></li>
                                    <li><a href="/pengajuan/resertifikasi"><i className="fa-solid fa-arrow-rotate-right"></i> Resertifikasi</a></li>
                                    <li><a href="/pengajuan/survailen"><i className="fa-solid fa-magnifying-glass"></i> Survailen</a></li>
                                </ul>
                            </li>

                            <li className={`nav-item-with-dropdown ${aktivitasActive || window.location.pathname.startsWith('/aktivitas') ? 'active' : ''}`}>
                                <a href="#" className="nav-link-dropdown" onClick={(e) => { e.preventDefault(); setAktivitasActive(!aktivitasActive); }}>
                                    <span>
                                        <i className="fa-solid fa-list-check"></i> Aktivitas
                                    </span>
                                    <i className="fa-solid fa-chevron-down dropdown-icon dropdown-toggle-arrow" style={{ cursor: 'pointer', padding: '5px' }}></i>
                                </a>
                                <ul className="dropdown-menu-sidebar">
                                    <li><a href="/aktivitas?filter=sertifikasi"><i className="fa-solid fa-certificate"></i> Sertifikasi</a></li>
                                    <li><a href="/aktivitas?filter=resertifikasi"><i className="fa-solid fa-arrow-rotate-right"></i> Resertifikasi</a></li>
                                    <li><a href="/aktivitas?filter=survailen"><i className="fa-solid fa-magnifying-glass"></i> Survailen</a></li>
                                </ul>
                            </li>
                            
                            <li><a href="/sertifikat"><i className="fa-solid fa-award"></i> Sertifikat</a></li>
                            <li><a href="/billing"><i className="fa-solid fa-file-invoice-dollar"></i> Billing</a></li>
                            <li><a href="/banding"><i className="fa-solid fa-gavel"></i> Banding & Keluhan</a></li>
                            <li><a href="/client/cs"><i className="fa-solid fa-headset"></i> Customer Service</a></li>
                        </>
                    ) : (
                        <>
                            <li>
                                <a href="/admin/dashboard" className={window.location.pathname === '/admin/dashboard' ? 'active' : ''}>
                                    <i className="fa-solid fa-gauge"></i> Dashboard
                                </a>
                            </li>
                            {/* Further admin menu items could be placed here... */}
                        </>
                    )}
                </ul>

                <div style={{ marginTop: 'auto', padding: '20px', borderTop: '1px solid rgba(255,255,255,0.1)' }}>
                    <a href="/" style={{ color: '#a7f3d0', textDecoration: 'none', display: 'flex', alignItems: 'center', gap: '10px', fontWeight: 500, marginBottom: '15px', fontSize: '14px' }}>
                        <i className="fa-solid fa-house"></i> Kembali ke Beranda
                    </a>
                    <Link href="/logout" method="post" as="button" style={{ background: 'none', border: 'none', color: '#f87171', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '10px', fontWeight: 600 }}>
                        <i className="fa-solid fa-power-off"></i> Keluar
                    </Link>
                </div>
            </aside>

            <div className={`overlay ${sidebarActive && window.innerWidth < 992 ? 'active' : ''}`} id="overlay" onClick={() => setSidebarActive(false)}></div>

            <div className="top-navbar">
                <div style={{ display: 'flex', alignItems: 'center', gap: '20px', flex: 1 }}>
                    <i className="fa-solid fa-bars" id="menu-toggle" onClick={toggleSidebar} style={{ cursor: 'pointer' }}></i>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                    <div className="dropdown">
                        <button 
                            className="user-profile-top dropdown-toggle" 
                            type="button" 
                            id="dropdownNotifMenu" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false" 
                            style={{ position: 'relative' }}
                            onClick={() => {
                                fetch('/notifikasi/mark-all-read', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                }).then(res => res.json()).then(data => {
                                    if(data.success) {
                                        const badge = document.getElementById('react-notif-badge');
                                        if(badge) badge.remove();
                                    }
                                }).catch(e => console.error(e));
                            }}
                        >
                            <i className="fa-solid fa-bell" style={{ fontSize: '22px', color: '#64748b' }}></i>
                            {user?.email === 'client@lspro.local' && (
                                <span id="react-notif-badge" className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style={{ fontSize: '9px', padding: '3px 5px' }}>
                                    3
                                </span>
                            )}
                        </button>
                        <ul className="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="dropdownNotifMenu" style={{ width: '300px', padding: '0' }}>
                            <li className="px-3 py-2 border-bottom fw-bold" style={{ fontSize: '13px', background: '#f8fafc' }}>
                                Notifikasi Terbaru
                            </li>
                            {user?.email === 'client@lspro.local' ? (
                                <>
                                    <li><a className="dropdown-item py-2 border-bottom" href="#" style={{ fontSize: '12px', whiteSpace: 'normal' }}><strong>Evaluasi Sistem</strong> dokumen Anda telah selesai.</a></li>
                                    <li><a className="dropdown-item py-2 border-bottom" href="#" style={{ fontSize: '12px', whiteSpace: 'normal' }}><strong>Penerbitan Sertifikat</strong> Anda sedang diproses.</a></li>
                                    <li><a className="dropdown-item py-2 border-bottom" href="#" style={{ fontSize: '12px', whiteSpace: 'normal' }}>Selamat datang di layanan <strong>LSPro</strong>.</a></li>
                                </>
                            ) : (
                                <li className="text-center py-4 text-muted" style={{ fontSize: '12px' }}>
                                    Belum ada notifikasi baru
                                </li>
                            )}
                            <li>
                                <a className="dropdown-item py-2 text-center fw-bold text-primary" href="/notifikasi" style={{ fontSize: '12px' }}>
                                    Lihat Semua Notifikasi
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div className="dropdown">
                        <button className="user-profile-top dropdown-toggle" type="button" id="dropdownProfileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <div style={{ textAlign: 'right', marginRight: '12px' }}>
                            <span style={{ fontWeight: 700, color: '#1e293b', fontSize: '14px', display: 'block' }}>
                                {user?.nama_penghubung || user?.email}
                            </span>
                            <span style={{ display: 'block', fontSize: '11px', color: '#16a34a', fontWeight: 600 }}>
                                {user?.nama_perusahaan || 'CLIENT'}
                            </span>
                        </div>
                        <i className="fa-solid fa-circle-user" style={{ fontSize: '32px', color: '#cbd5e1' }}></i>
                    </button>
                    
                    <ul className="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="dropdownProfileMenu">
                        <li className="px-3 py-2 border-bottom mb-1">
                            <div className="fw-bold text-dark" style={{ fontSize: '13px' }}>{user.name}</div>
                            <div className="text-muted" style={{ fontSize: '11px', textTransform: 'lowercase' }}>{user.email}</div>
                        </li>
                        <li>
                            <a href="/profile" className="dropdown-item d-flex align-items-center gap-2 py-2" style={{ fontSize: '13px', color: '#334155' }}>
                                <i className="fa-solid fa-user-gear text-secondary" style={{ width: '16px' }}></i> Pengaturan Profile
                            </a>
                        </li>
                        <li><hr className="dropdown-divider my-1" /></li>
                        <li>
                            <Link href="/logout" method="post" as="button" className="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" style={{ fontSize: '13px', fontWeight: 500, width: '100%', textAlign: 'left', border: 'none', background: 'none' }}>
                                <i className="fa-solid fa-right-from-bracket" style={{ width: '16px' }}></i> Keluar Aplikasi
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

            <main className="main-content">
                {children}
            </main>
        </>
    );
}
