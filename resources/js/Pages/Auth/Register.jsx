import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import SquigglyLoader from '@/Components/SquigglyLoader';

export default function Register({ errors: serverErrors }) {
    const [showPassword, setShowPassword] = React.useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = React.useState(false);

    const { data, setData, post, processing, errors } = useForm({
        nama_perusahaan: '',
        nama_penghubung: '',
        email: '',
        password: '',
        password_confirmation: '',
        alamat: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <>
            <Head title="Buat Akun - LSPro BRMP">
                <link rel="stylesheet" href="/css/login_style.css" />
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
            </Head>

            <div style={{ display: 'flex', height: '100vh', width: '100%', fontFamily: "'Inter', sans-serif" }}>
                <div className="auth-left" style={{ overflowY: 'auto' }}>
                    <div className="auth-header">
                        <img src="/assets/kementan.png" alt="Logo Kementan" style={{ width: '55px', height: 'auto' }} />
                        <h2>BALAI BESAR PERAKITAN DAN MODERNISASI SUMBER DAYA LAHAN PERTANIAN<br/>
                            <span style={{ fontSize: '10px', fontWeight: 'normal', color: '#64748b' }}>BADAN PERAKITAN DAN MODERNISASI PERTANIAN</span>
                        </h2>
                    </div>

                    <div style={{ marginTop: '20px' }}>
                        <h1 className="auth-title">Pendaftaran Akun Klien</h1>
                        <p className="auth-subtitle">Lengkapi formulir di bawah ini untuk mengajukan sertifikasi LSPro.</p>

                        {(serverErrors && Object.keys(serverErrors).length > 0) && (
                            <div style={{ background: '#fee2e2', color: '#ef4444', padding: '12px 15px', borderRadius: '8px', marginBottom: '20px', fontSize: '13px', borderLeft: '4px solid #ef4444' }}>
                                <i className="fa-solid fa-circle-exclamation" style={{ marginRight: '8px' }}></i> Pastikan semua data diisi dengan benar.
                            </div>
                        )}

                        <form onSubmit={submit}>
                            <div className="form-group">
                                <label>Nama Perusahaan / Instansi *</label>
                                <div className="input-icon-wrapper">
                                    <i className="fa-solid fa-building"></i>
                                    <input 
                                        type="text" 
                                        name="nama_perusahaan" 
                                        className="form-control" 
                                        placeholder="Masukkan nama perusahaan..." 
                                        value={data.nama_perusahaan}
                                        onChange={(e) => setData('nama_perusahaan', e.target.value)}
                                        required 
                                        autoFocus 
                                    />
                                </div>
                                {errors.nama_perusahaan && <span style={{ color: '#ef4444', fontSize: '12px', marginTop: '5px', display: 'block' }}>{errors.nama_perusahaan}</span>}
                            </div>

                            <div className="form-group">
                                <label>Nama Lengkap Penghubung (PIC) *</label>
                                <div className="input-icon-wrapper">
                                    <i className="fa-solid fa-user"></i>
                                    <input 
                                        type="text" 
                                        name="nama_penghubung" 
                                        className="form-control" 
                                        placeholder="Masukkan nama lengkap..." 
                                        value={data.nama_penghubung}
                                        onChange={(e) => setData('nama_penghubung', e.target.value)}
                                        required 
                                    />
                                </div>
                                {errors.nama_penghubung && <span style={{ color: '#ef4444', fontSize: '12px', marginTop: '5px', display: 'block' }}>{errors.nama_penghubung}</span>}
                            </div>


                            <div className="form-group">
                                <label>E-mail *</label>
                                <div className="input-icon-wrapper">
                                    <i className="fa-solid fa-envelope"></i>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        className="form-control" 
                                        placeholder="contoh@mail.dev" 
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required 
                                    />
                                </div>
                                {errors.email && <span style={{ color: '#ef4444', fontSize: '12px', marginTop: '5px', display: 'block' }}>{errors.email}</span>}
                            </div>

                            <div className="form-group">
                                <label>Password *</label>
                                <div className="input-icon-wrapper" style={{ position: 'relative' }}>
                                    <i className="fa-solid fa-lock"></i>
                                    <input 
                                        type={showPassword ? "text" : "password"} 
                                        name="password" 
                                        className="form-control" 
                                        placeholder="Minimal 6 karakter" 
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        required 
                                        style={{ paddingRight: '40px' }}
                                    />
                                    <i 
                                        className={`fa-solid ${showPassword ? 'fa-eye-slash' : 'fa-eye'}`} 
                                        style={{ position: 'absolute', right: '15px', left: 'auto', top: '50%', transform: 'translateY(-50%)', cursor: 'pointer', color: '#94a3b8', zIndex: 10 }}
                                        onClick={() => setShowPassword(!showPassword)}
                                    ></i>
                                </div>
                                {errors.password && <span style={{ color: '#ef4444', fontSize: '12px', marginTop: '5px', display: 'block' }}>{errors.password}</span>}
                            </div>

                            <div className="form-group">
                                <label>Konfirmasi Password *</label>
                                <div className="input-icon-wrapper" style={{ position: 'relative' }}>
                                    <i className="fa-solid fa-lock"></i>
                                    <input 
                                        type={showConfirmPassword ? "text" : "password"} 
                                        name="password_confirmation" 
                                        className="form-control" 
                                        placeholder="Tulis kembali password" 
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        required 
                                        style={{ paddingRight: '40px' }}
                                    />
                                    <i 
                                        className={`fa-solid ${showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'}`} 
                                        style={{ position: 'absolute', right: '15px', left: 'auto', top: '50%', transform: 'translateY(-50%)', cursor: 'pointer', color: '#94a3b8', zIndex: 10 }}
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                    ></i>
                                </div>
                            </div>

                            <div className="form-group">
                                <label>Alamat Lengkap *</label>
                                <div className="input-icon-wrapper">
                                    <i className="fa-solid fa-map-location-dot"></i>
                                    <input 
                                        type="text" 
                                        name="alamat" 
                                        className="form-control" 
                                        placeholder="Masukkan alamat perusahaan..." 
                                        value={data.alamat}
                                        onChange={(e) => setData('alamat', e.target.value)}
                                        required 
                                    />
                                </div>
                                {errors.alamat && <span style={{ color: '#ef4444', fontSize: '12px', marginTop: '5px', display: 'block' }}>{errors.alamat}</span>}
                            </div>

                            <div className="form-group" style={{ marginTop: '25px' }}>
                                <label>Kebijakan Privasi</label>
                                <div style={{ height: '85px', overflowY: 'auto', border: '1.5px solid #e2e8f0', borderRadius: '10px', padding: '12px', fontSize: '12px', color: '#475569', background: '#f8fafc', marginBottom: '12px', lineHeight: 1.6 }}>
                                    <strong>KEMENTERIAN PERTANIAN - KEBIJAKAN PRIVASI</strong><br/>
                                    Saya yang mendaftar di sistem ini menyetujui bahwa:<br/>
                                    1. Data pribadi dan perusahaan yang dimasukkan ke dalam sistem ini akan digunakan semata-mata untuk keperluan administrasi dan proses Sertifikasi Produk.<br/>
                                    2. Pihak LSPro BBPM SDLP berkomitmen untuk menjaga kerahasiaan data yang diunggah dan tidak akan menyebarluaskan kepada pihak ketiga tanpa persetujuan klien, kecuali diwajibkan oleh undang-undang.
                                </div>
                                <label style={{ display: 'flex', alignItems: 'flex-start', gap: '10px', fontWeight: 'normal', cursor: 'pointer', fontSize: '13px', color: '#334155' }}>
                                    <input type="checkbox" required style={{ width: '18px', height: '18px', cursor: 'pointer', marginTop: '2px' }} />
                                    <span>Saya telah membaca, memahami, dan menyetujui isi Kebijakan Privasi di atas.</span>
                                </label>
                            </div>

                            <motion.button 
                                whileHover={{ scale: 1.05 }}
                                type="submit" 
                                className="btn-auth mt-4"
                                disabled={processing}
                                style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '50px' }}
                            >
                                {processing ? (
                                    <SquigglyLoader color="#ffffff" />
                                ) : (
                                    <>Daftar Sekarang &rarr;</>
                                )}
                            </motion.button>
                        </form>

                        <p style={{ textAlign: 'center', marginTop: '30px', fontSize: '14px', color: '#64748b' }}>
                            Sudah punya akun? <Link href="/login" style={{ color: '#2563eb', fontWeight: 600, textDecoration: 'none' }}>Masuk di sini</Link>
                        </p>
                    </div>
                </div>

                <div className="auth-right"></div>
            </div>
        </>
    );
}
