import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function CS({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Customer Service</h2>}
        >
            <Head title="Customer Service" />

            <div className="container-fluid" style={{ padding: '24px' }}>
                <div className="card" style={{ borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)' }}>
                    <div className="card-header" style={{ background: '#fff', borderBottom: '1px solid #f1f5f9', padding: '20px 24px', borderTopLeftRadius: '12px', borderTopRightRadius: '12px' }}>
                        <h5 style={{ margin: 0, fontWeight: 700, color: '#1e293b' }}>
                            <i className="fa-solid fa-headset me-2" style={{ color: '#2563eb' }}></i>
                            Layanan Customer Service
                        </h5>
                    </div>
                    <div className="card-body" style={{ padding: '40px 24px', textAlign: 'center' }}>
                        <div style={{ maxWidth: '600px', margin: '0 auto' }}>
                            <div style={{ width: '80px', height: '80px', borderRadius: '50%', background: '#eff6ff', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 24px auto' }}>
                                <i className="fa-solid fa-comments text-primary" style={{ fontSize: '32px' }}></i>
                            </div>
                            <h4 style={{ fontWeight: 700, color: '#1e293b', marginBottom: '16px' }}>Butuh Bantuan?</h4>
                            <p style={{ color: '#64748b', fontSize: '15px', lineHeight: 1.6, marginBottom: '32px' }}>
                                Tim Customer Service LSPro BRMP SDLP siap membantu Anda setiap hari kerja (Senin - Jumat, 08.00 - 16.00 WIB). Silakan hubungi kami melalui kontak di bawah ini.
                            </p>
                            
                            <div className="row g-4 justify-content-center">
                                <div className="col-md-6">
                                    <div style={{ padding: '20px', background: '#f8fafc', borderRadius: '12px', border: '1px solid #e2e8f0' }}>
                                        <i className="fa-brands fa-whatsapp mb-3" style={{ fontSize: '28px', color: '#22c55e' }}></i>
                                        <h6 style={{ fontWeight: 700 }}>WhatsApp</h6>
                                        <p style={{ margin: 0, fontSize: '14px', color: '#475569' }}>+62 812-3456-7890</p>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div style={{ padding: '20px', background: '#f8fafc', borderRadius: '12px', border: '1px solid #e2e8f0' }}>
                                        <i className="fa-solid fa-envelope mb-3" style={{ fontSize: '28px', color: '#3b82f6' }}></i>
                                        <h6 style={{ fontWeight: 700 }}>Email</h6>
                                        <p style={{ margin: 0, fontSize: '14px', color: '#475569' }}>cs@lspro.bbsdlp.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
