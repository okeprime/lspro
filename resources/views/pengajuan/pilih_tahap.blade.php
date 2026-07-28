@extends('layouts.app')

@section('title', 'Pilih Jenis Layanan Pengajuan Sertifikasi')

@section('extra-css')
<style>
    .service-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .service-card.selected {
        background-color: #f8faff;
        transform: scale(1.02);
    }
    .service-card.selected .check-icon {
        opacity: 1;
        transform: scale(1);
    }
    .check-icon {
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
        font-size: 24px;
        position: absolute;
        top: 15px;
        right: 15px;
    }
    .step-container {
        display: none;
        animation: fadeIn 0.5s ease;
    }
    .step-container.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .detail-box {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left: 5px solid #0d6efd;
    }
    
    /* Timeline CSS */
    .flow-timeline {
        position: relative;
        max-width: 850px;
        margin: 0 auto 50px auto;
        padding: 20px 0;
        display: flex;
        justify-content: space-between;
    }
    .flow-timeline::before {
        content: '';
        position: absolute;
        top: 45px;
        left: 10%;
        right: 10%;
        height: 4px;
        background: #e2e8f0;
        z-index: 1;
        border-radius: 2px;
    }
    .timeline-step {
        position: relative;
        z-index: 2;
        width: 20%;
        text-align: center;
    }
    .timeline-icon {
        width: 50px;
        height: 50px;
        background: #fff;
        border: 4px solid #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px auto;
        font-size: 18px;
        color: #94a3b8;
        transition: all 0.3s;
    }
    .timeline-step:hover .timeline-icon {
        border-color: #0d6efd;
        color: #0d6efd;
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }
    .timeline-title {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }
    @media (max-width: 768px) {
        .flow-timeline::before { display: none; }
        .flow-timeline { display: flex; flex-direction: column; gap: 15px; }
        .timeline-step { width: 100%; display: flex; align-items: center; text-align: left; gap: 15px; }
        .timeline-icon { margin: 0; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div style="margin-bottom: 30px; text-align: center;">
        <h2 style="color: #1e293b; font-weight: 700;">Alur Pengajuan Sertifikasi Sertifikat Kesesuaian SNI</h2>
        <p style="color: #64748b; font-size: 15px; margin-top: 5px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Ikuti panduan interaktif berikut untuk memilih jenis layanan yang sesuai dengan kebutuhan Anda.
        </p>
    </div>

    <!-- Visual Alur Sertifikasi -->
    <div class="flow-timeline">
        <div class="timeline-step">
            <div class="timeline-icon">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="timeline-title">1. Pendaftaran</div>
        </div>
        <div class="timeline-step">
            <div class="timeline-icon">
                <i class="fa-solid fa-file-circle-check"></i>
            </div>
            <div class="timeline-title">2. Tinjauan Dokumen</div>
        </div>
        <div class="timeline-step">
            <div class="timeline-icon">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
            </div>
            <div class="timeline-title">3. Audit & Uji</div>
        </div>
        <div class="timeline-step">
            <div class="timeline-icon">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div class="timeline-title">4. Evaluasi</div>
        </div>
        <div class="timeline-step">
            <div class="timeline-icon">
                <i class="fa-solid fa-award"></i>
            </div>
            <div class="timeline-title">5. Sertifikat SPPT</div>
        </div>
    </div>

    <!-- Step 1: Pemilihan Layanan -->
    <div id="step-1" class="step-container active">
        <h4 class="mb-4 text-center" style="color: #334155; font-weight: 600;">Langkah 1: Pilih Jenis Layanan</h4>
        
        <div class="row g-4 justify-content-center">
            
            <!-- Option 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100 info-card" data-target="info-1" data-url="{{ route('pengajuan.buat', ['tahap' => '1']) }}" style="border-top: 5px solid #198754; position: relative; overflow: hidden; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-circle-check check-icon" style="color: #198754;"></i>
                    <div style="position: absolute; bottom: -10px; right: -10px; color: #f1f5f9; font-size: 80px; font-weight: 800; opacity: 0.2; line-height: 1;">1</div>
                    <div class="mb-3">
                        <div style="width: 50px; height: 50px; background: rgba(25, 135, 84, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fa-solid fa-file-circle-plus" style="color: #198754; font-size: 24px;"></i>
                        </div>
                        <h4 style="font-size: 18px; color: #1e293b; font-weight: 700; margin-bottom: 8px;">Pengajuan Baru</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 0;">Sertifikasi awal Sertifikat Kesesuaian SNI untuk produk baru.</p>
                    </div>
                </div>
            </div>

            <!-- Option 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100 info-card" data-target="info-2" data-url="{{ route('pengajuan.buat', ['tahap' => '2']) }}" style="border-top: 5px solid #0d6efd; position: relative; overflow: hidden; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-circle-check check-icon" style="color: #0d6efd;"></i>
                    <div style="position: absolute; bottom: -10px; right: -10px; color: #f1f5f9; font-size: 80px; font-weight: 800; opacity: 0.2; line-height: 1;">2</div>
                    <div class="mb-3">
                        <div style="width: 50px; height: 50px; background: rgba(13, 110, 253, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fa-solid fa-shield-halved" style="color: #0d6efd; font-size: 24px;"></i>
                        </div>
                        <h4 style="font-size: 18px; color: #1e293b; font-weight: 700; margin-bottom: 8px;">Survailen</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 0;">Pengawasan berkala untuk sertifikat aktif.</p>
                    </div>
                </div>
            </div>

            <!-- Option 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100 info-card" data-target="info-3" data-url="{{ route('pengajuan.buat', ['tahap' => '3']) }}" style="border-top: 5px solid #ffc107; position: relative; overflow: hidden; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-circle-check check-icon" style="color: #ffc107;"></i>
                    <div style="position: absolute; bottom: -10px; right: -10px; color: #f1f5f9; font-size: 80px; font-weight: 800; opacity: 0.2; line-height: 1;">3</div>
                    <div class="mb-3">
                        <div style="width: 50px; height: 50px; background: rgba(255, 193, 7, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fa-solid fa-triangle-exclamation" style="color: #b58500; font-size: 24px;"></i>
                        </div>
                        <h4 style="font-size: 18px; color: #1e293b; font-weight: 700; margin-bottom: 8px;">Resertifikasi</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 0;">Perpanjangan atau perubahan ruang lingkup sertifikat.</p>
                    </div>
                </div>
            </div>

            <!-- Option 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100 info-card" data-target="info-4" data-url="{{ route('pengajuan.buat', ['tahap' => '4']) }}" style="border-top: 5px solid #dc3545; position: relative; overflow: hidden; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-circle-check check-icon" style="color: #dc3545;"></i>
                    <div style="position: absolute; bottom: -10px; right: -10px; color: #f1f5f9; font-size: 80px; font-weight: 800; opacity: 0.2; line-height: 1;">4</div>
                    <div class="mb-3">
                        <div style="width: 50px; height: 50px; background: rgba(220, 53, 69, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fa-solid fa-comments" style="color: #dc3545; font-size: 24px;"></i>
                        </div>
                        <h4 style="font-size: 18px; color: #1e293b; font-weight: 700; margin-bottom: 8px;">Keluhan & Banding</h4>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 0;">Pengaduan resmi atau banding hasil evaluasi.</p>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="text-center mt-5">
            <button id="btn-next" class="btn btn-primary px-5 py-2 fw-bold" disabled style="border-radius: 10px; transition: 0.3s; background: #0d6efd; border: none; font-size: 16px;">
                Lanjutkan <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Konfirmasi & Detail -->
    <div id="step-2" class="step-container mt-4">
        <div class="d-flex align-items-center mb-4" id="btn-back" style="color: #64748b; font-weight: 600; cursor: pointer; display: inline-block;">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Pilihan Layanan
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="detail-box" id="detail-box-container">
                    <div id="info-1" class="service-info" style="display: none;">
                        <h3 style="color: #198754; font-weight: 700;"><i class="fa-solid fa-file-circle-plus me-2"></i> Pengajuan Sertifikasi Baru</h3>
                        <p class="text-muted mt-3">Anda memilih untuk mendaftarkan produk baru untuk mendapatkan Sertifikat Kesesuaian SNI. Proses ini meliputi evaluasi dokumen, audit pabrik, dan pengujian sampel.</p>
                        <h5 class="mt-4 fw-bold">Persiapan Dokumen:</h5>
                        <ul class="text-muted" style="line-height: 1.8;">
                            <li>Akte Pendirian Perusahaan & Izin Usaha Industri (IUI)</li>
                            <li>Sertifikat Merek / Bukti Pendaftaran Merek</li>
                            <li>Struktur Organisasi & Alur Produksi</li>
                            <li>Pedoman Mutu & Daftar Prosedur</li>
                        </ul>
                    </div>

                    <div id="info-2" class="service-info" style="display: none;">
                        <h3 style="color: #0d6efd; font-weight: 700;"><i class="fa-solid fa-shield-halved me-2"></i> Survailen</h3>
                        <p class="text-muted mt-3">Anda memilih layanan Survailen. Layanan ini diwajibkan bagi perusahaan yang telah memiliki Sertifikat Kesesuaian SNI aktif untuk memastikan konsistensi mutu produk.</p>
                        <h5 class="mt-4 fw-bold">Ketentuan:</h5>
                        <ul class="text-muted" style="line-height: 1.8;">
                            <li>Survailen dilakukan sekurang-kurangnya 1 (satu) kali dalam setahun.</li>
                            <li>Menyiapkan rekaman mutu terbaru dan laporan produksi.</li>
                        </ul>
                    </div>

                    <div id="info-3" class="service-info" style="display: none;">
                        <h3 style="color: #ffc107; font-weight: 700;"><i class="fa-solid fa-triangle-exclamation me-2" style="color: #b58500;"></i> Resertifikasi / Ruang Lingkup</h3>
                        <p class="text-muted mt-3">Anda memilih layanan Resertifikasi atau Perubahan Ruang Lingkup. Gunakan layanan ini jika sertifikat Anda akan segera habis masa berlakunya atau terdapat perubahan pada spesifikasi produk.</p>
                        <h5 class="mt-4 fw-bold">Ketentuan:</h5>
                        <ul class="text-muted" style="line-height: 1.8;">
                            <li>Diajukan minimal 3 bulan sebelum masa berlaku sertifikat habis.</li>
                            <li>Melampirkan Sertifikat Kesesuaian SNI yang lama.</li>
                        </ul>
                    </div>

                    <div id="info-4" class="service-info" style="display: none;">
                        <h3 style="color: #dc3545; font-weight: 700;"><i class="fa-solid fa-comments me-2"></i> Keluhan & Banding</h3>
                        <p class="text-muted mt-3">Anda memilih layanan Keluhan & Banding. Layanan ini memfasilitasi Anda untuk mengajukan keberatan terhadap hasil keputusan sertifikasi atau keluhan terhadap layanan kami.</p>
                        <h5 class="mt-4 fw-bold">Ketentuan:</h5>
                        <ul class="text-muted" style="line-height: 1.8;">
                            <li>Disertai dengan bukti-bukti dan argumen yang kuat.</li>
                            <li>Banding diajukan maksimal 14 hari kerja setelah keputusan ditetapkan.</li>
                        </ul>
                    </div>

                    <div class="mt-5 text-center bg-light p-4 rounded-3 border">
                        <p class="mb-3 text-muted">Apakah Anda sudah siap untuk mengisi formulir pengajuan?</p>
                        <a href="#" id="btn-submit-final" class="btn btn-primary px-5 py-3 fw-bold shadow-sm" style="border-radius: 10px; font-size: 16px;">
                            Mulai Pengisian Formulir <i class="fa-solid fa-paper-plane ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 40px; background: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 10px; display: flex; align-items: center; gap: 15px; max-width: 800px; margin-left: auto; margin-right: auto;">
        <i class="fa-solid fa-circle-info" style="color: #d97706; font-size: 20px;"></i>
        <p style="color: #92400e; font-size: 13px; margin: 0;">Pastikan data profil perusahaan Anda sudah lengkap di menu <b>Pengaturan Profile</b> sebelum melakukan pengajuan untuk hasil dokumen otomatis yang maksimal.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.service-card');
    const btnNext = document.getElementById('btn-next');
    const btnBack = document.getElementById('btn-back');
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const btnSubmitFinal = document.getElementById('btn-submit-final');
    const detailBoxContainer = document.getElementById('detail-box-container');
    
    let selectedTarget = null;
    let selectedUrl = null;
    let selectedColor = null;

    cards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all
            cards.forEach(c => {
                c.classList.remove('selected');
                c.style.borderColor = 'transparent';
            });
            
            // Add selected class to clicked
            this.classList.add('selected');
            
            // Highlight color based on the original border-top color
            selectedColor = this.style.borderTopColor;
            this.style.borderColor = selectedColor;
            
            // Enable next button
            btnNext.disabled = false;
            
            // Store target info and url
            selectedTarget = this.getAttribute('data-target');
            selectedUrl = this.getAttribute('data-url');
        });
    });

    btnNext.addEventListener('click', function() {
        if(!selectedTarget || !selectedUrl) return;
        
        // Hide all info
        document.querySelectorAll('.service-info').forEach(info => {
            info.style.display = 'none';
        });
        
        // Show selected info
        document.getElementById(selectedTarget).style.display = 'block';
        
        // Set dynamic border color for detail box
        if (selectedColor) {
            detailBoxContainer.style.borderLeftColor = selectedColor;
            btnSubmitFinal.style.backgroundColor = selectedColor;
            btnSubmitFinal.style.borderColor = selectedColor;
        }
        
        // Set final URL
        btnSubmitFinal.setAttribute('href', selectedUrl);
        
        // Switch steps
        step1.classList.remove('active');
        setTimeout(() => {
            step2.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 300);
    });

    btnBack.addEventListener('click', function() {
        step2.classList.remove('active');
        setTimeout(() => {
            step1.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 300);
    });
});
</script>
@endsection
