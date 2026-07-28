import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import SquigglyLoader from '@/Components/SquigglyLoader';

export default function Login({ errors: serverErrors }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
    });

    const [showPassword, setShowPassword] = React.useState(false);

    const submit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Masuk - LSPro BRMP">
                <link rel="stylesheet" href="/css/login_style.css" />
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
            </Head>

            <div style={{ display: 'flex', height: '100vh', width: '100%', fontFamily: "'Inter', sans-serif" }}>
                <div className="auth-left">
                    <div className="auth-header">
                        <img src="/assets/kementan.png" alt="Logo Kementan" style={{ width: '55px', height: 'auto' }} />
                        <h2>BALAI BESAR PERAKITAN DAN MODERNISASI SUMBER DAYA LAHAN PERTANIAN<br/>
                            <span style={{ fontSize: '10px', fontWeight: 'normal', color: '#64748b' }}>BADAN PERAKITAN DAN MODERNISASI PERTANIAN</span>
                        </h2>
                    </div>

                    <div style={{ marginTop: '20px' }}>
                        <h1 className="auth-title">Selamat Datang Kembali</h1>
                        <p className="auth-subtitle">Silakan masukkan email dan password Anda untuk masuk ke sistem.</p>

                        {(serverErrors && Object.keys(serverErrors).length > 0) && (
                            <div style={{ background: '#fee2e2', color: '#ef4444', padding: '12px 15px', borderRadius: '8px', marginBottom: '20px', fontSize: '13px', borderLeft: '4px solid #ef4444' }}>
                                <i className="fa-solid fa-circle-exclamation" style={{ marginRight: '8px' }}></i> {serverErrors[Object.keys(serverErrors)[0]]}
                            </div>
                        )}

                        <form onSubmit={submit}>
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
                                        autoFocus 
                                    />
                                </div>
                                {errors.email && <div style={{ color: '#ef4444', fontSize: '12px', marginTop: '4px' }}>{errors.email}</div>}
                            </div>

                            <div className="form-group">
                                <label>Password *</label>
                                <div className="input-icon-wrapper" style={{ position: 'relative' }}>
                                    <i className="fa-solid fa-lock"></i>
                                    <input 
                                        type={showPassword ? "text" : "password"} 
                                        name="password" 
                                        className="form-control" 
                                        placeholder="••••••••" 
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
                            </div>

                            <div style={{ textAlign: 'right', marginBottom: '20px' }}>
                                <a href="#" style={{ fontSize: '13px', color: '#2563eb', textDecoration: 'none', fontWeight: 500 }}>Lupa Password?</a>
                            </div>

                            <motion.button 
                                whileHover={{ scale: 1.05 }}
                                type="submit" 
                                className="btn-auth"
                                disabled={processing}
                                style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '50px' }}
                            >
                                {processing ? (
                                    <SquigglyLoader color="#ffffff" />
                                ) : (
                                    <>Masuk Sekarang &rarr;</>
                                )}
                            </motion.button>
                        </form>

                        <p style={{ textAlign: 'center', marginTop: '30px', fontSize: '14px', color: '#64748b' }}>
                            Belum punya akun? <Link href="/register" style={{ color: '#2563eb', fontWeight: 600, textDecoration: 'none' }}>Daftar di sini</Link>
                        </p>
                    </div>
                </div>

                <div className="auth-right"></div>
            </div>
        </>
    );
}
