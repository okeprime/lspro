@extends('layouts.app')

@section('title', 'Formulir Pengajuan Sertifikasi')

@section('extra-css')
<style>
    .form-card-wrapper {
        background: white;
        padding: 35px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .form-header-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    .btn-back-green {
        text-decoration: none; 
        color: #16a34a; 
        font-size: 22px; 
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-back-green:hover {
        color: #15803d;
        transform: translateX(-3px);
    }
    .section-divider-title {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-control-custom {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 14px;
        transition: 0.2s;
    }
    .form-control-custom:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    .form-label-weight {
        font-weight: 600;
        color: #334155;
        font-size: 13px;
        margin-bottom: 8px;
    }
    .btn-submit-green {
        background: #16a34a;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-submit-green:hover {
        background: #15803d;
        color: white;
        transform: translateY(-1px);
    }
    .btn-cancel-gray {
        background: white;
        color: #64748b;
        border: 1px solid #cbd5e1;
        padding: 12px 25px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .btn-cancel-gray:hover {
        background: #f8fafc;
        color: #334155;
    }
    .sub-section-title {
        font-size: 12px;
        font-weight: bold;
        color: #16a34a;
        text-transform: uppercase;
        margin-top: 15px;
        margin-bottom: 10px;
        border-bottom: 1px dashed #cbd5e1;
        padding-bottom: 3px;
    }

    /* Stepper Styling */
    .wizard-stepper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }
    .wizard-stepper::before {
        content: '';
        position: absolute;
        top: 22px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .wizard-stepper-progress {
        position: absolute;
        top: 22px;
        left: 0;
        height: 3px;
        background: #16a34a;
        z-index: 2;
        width: 0%;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .wizard-step {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        flex: 1;
    }
    .wizard-step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: white;
        border: 3px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #94a3b8;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    .wizard-step.active .wizard-step-circle {
        border-color: #16a34a;
        color: #16a34a;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.15);
    }
    .wizard-step.completed .wizard-step-circle {
        background: #16a34a;
        border-color: #16a34a;
        color: white;
    }
    .wizard-step-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-align: center;
        line-height: 1.2;
    }
    .wizard-step.active .wizard-step-label {
        color: #16a34a;
        font-weight: 700;
    }
    .wizard-step.completed .wizard-step-label {
        color: #334155;
    }

    /* Step Sections */
    .step-section {
        display: none;
    }
    .step-section.active {
        display: block;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Auto Save Toast */
    #autosave-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #1e293b;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 13px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    #autosave-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    
    .summary-table th { background: #f8fafc; font-weight: 600; color: #475569; width: 35%; }
    .summary-table td { color: #0f172a; }
</style>
@endsection

@section('content')
@php
    $val = function($field, $default = '') use ($formData) {
        return old($field, $formData[$field] ?? $default);
    };
@endphp
<div class="container py-4">
    <div class="form-card-wrapper">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('pengajuan.index') }}" class="btn-back-green">
                    <i class="fa-solid fa-circle-arrow-left"></i>
                </a>
                <div>
                    <h3 style="color: #1e293b; font-weight: 700; margin: 0; font-size: 22px;">Formulir Sertifikasi Otomatis</h3>
                    <p style="color: #64748b; font-size: 13px; margin: 5px 0 0 0;">Lengkapi formulir permohonan sertifikasi secara bertahap (I - V).</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" id="btnManualSave">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Draft
                </button>
            </div>
        </div>

        @if($jenisSertifikasi)
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between" style="border-radius: 12px; background-color: #f0fdf4; color: #115e59;">
                <div>
                    <i class="fa-solid fa-leaf me-2"></i> Kategori Produk Sertifikasi: <strong>{{ $jenisSertifikasi }}</strong>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px; font-size: 14px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Stepper Progress --}}
        <div class="wizard-stepper">
            <div class="wizard-stepper-progress" id="stepProgress"></div>
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-circle">I</div>
                <div class="wizard-step-label">Pemohon</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-circle">II</div>
                <div class="wizard-step-label">Perusahaan & Pabrik</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-circle">III</div>
                <div class="wizard-step-label">Spesifikasi Produk</div>
            </div>
            <div class="wizard-step" data-step="4">
                <div class="wizard-step-circle">IV</div>
                <div class="wizard-step-label">SMM & Tenaga Kerja</div>
            </div>
            <div class="wizard-step" data-step="5">
                <div class="wizard-step-circle">V</div>
                <div class="wizard-step-label">Pernyataan & Kirim</div>
            </div>
        </div>

        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data" id="formSertifikasi">
            @csrf
            <input type="hidden" name="tahap" value="{{ $tahap ?? '1' }}">
            <input type="hidden" name="jenis_sertifikasi" value="{{ $jenisSertifikasi ?? '' }}">
            <input type="hidden" name="draft_id" id="draft_id" value="{{ $draft->id ?? '' }}">

            {{-- STEP 1: PEMOHON & SURAT --}}
            <div class="step-section active" data-step="1">
                <div class="section-divider-title">
                    <i class="fa-solid fa-envelope-open-text"></i> KEPALA SURAT PERMOHONAN
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nomor Surat</label>
                        <input type="text" class="form-control form-control-custom bg-light text-muted fw-bold" 
                            value="{{ $formData['nomor_surat'] ?? 'Otomatis Dibuat Sistem' }}" readonly style="border-style: dashed;">
                        <small class="text-muted mt-1">*Nomor akan dibuat otomatis oleh sistem saat disubmit.</small>
                    </div>  
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Jumlah Lampiran <span class="text-danger">*</span></label>
                        <input type="text" name="lampiran" class="form-control form-control-custom" value="{{ $val('lampiran', '1 Berkas') }}" placeholder="Contoh: 1 Berkas" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Perihal Surat <span class="text-danger">*</span></label>
                        <input type="text" name="perihal" class="form-control form-control-custom" value="{{ $val('perihal', 'Permohonan Sertifikasi SPPT SNI') }}" required>
                    </div>
                </div>

                <div class="section-divider-title mt-4">
                    <i class="fa-solid fa-user-tie"></i> I. IDENTITAS PENANGGUNG JAWAB & PENGHUBUNG
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Nama Pemohon / Penanggung Jawab <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pemohon" class="form-control form-control-custom" value="{{ $val('nama_pemohon') }}" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Jabatan dalam Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan_pemohon" class="form-control form-control-custom" value="{{ $val('jabatan_pemohon') }}" placeholder="Contoh: Direktur Utama" required>
                    </div>
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Alamat Pemohon <span class="text-danger">*</span></label>
                        <textarea name="alamat_pemohon" rows="2" class="form-control form-control-custom" placeholder="Alamat lengkap rumah pemohon..." required>{{ $val('alamat_pemohon') }}</textarea>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nomor Telepon / Fax Pemohon <span class="text-danger">*</span></label>
                        <input type="text" name="telp_pemohon" class="form-control form-control-custom" value="{{ $val('telp_pemohon') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nomor HP (WhatsApp) <span class="text-danger">*</span></label>
                        <input type="text" name="hp_pemohon" class="form-control form-control-custom" value="{{ $val('hp_pemohon') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Kewarganegaraan <span class="text-danger">*</span></label>
                        <input type="text" name="kewarganegaraan_pemohon" class="form-control form-control-custom" value="{{ $val('kewarganegaraan_pemohon', 'Indonesia') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Status Pemohon <span class="text-danger">*</span></label>
                        <select name="status_pemohon" class="form-control form-control-custom" required>
                            <option value="Produsen" {{ $val('status_pemohon') == 'Produsen' ? 'selected' : '' }}>Produsen Dalam Negeri</option>
                            <option value="Importir/Agen" {{ $val('status_pemohon') == 'Importir/Agen' ? 'selected' : '' }}>Importir / Agen</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nama Penghubung Teknis (CP) <span class="text-danger">*</span></label>
                        <input type="text" name="nama_penghubung" class="form-control form-control-custom" value="{{ $val('nama_penghubung') }}" placeholder="Staff yang mengurus SNI" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Jabatan Penghubung Teknis <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan_penghubung" class="form-control form-control-custom" value="{{ $val('jabatan_penghubung') }}" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">HP Penghubung Teknis <span class="text-danger">*</span></label>
                        <input type="text" name="hp_penghubung" class="form-control form-control-custom" value="{{ $val('hp_penghubung') }}" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">E-mail Penghubung Teknis <span class="text-danger">*</span></label>
                        <input type="email" name="email_penghubung" class="form-control form-control-custom" value="{{ $val('email_penghubung') }}" required>
                    </div>
                </div>
            </div>

            {{-- STEP 2: PERUSAHAAN, PABRIK & IMPORTIR --}}
            <div class="step-section" data-step="2">
                <div class="section-divider-title">
                    <i class="fa-solid fa-building"></i> II. IDENTITAS KANTOR & BADAN HUKUM
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_perusahaan" class="form-control form-control-custom" value="{{ $val('nama_perusahaan') }}" placeholder="Contoh: PT. Pupuk Subur Makmur" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Bentuk Badan Hukum <span class="text-danger">*</span></label>
                        <input type="text" name="badan_hukum" class="form-control form-control-custom" value="{{ $val('badan_hukum') }}" placeholder="Contoh: PT / CV / Koperasi" required>
                    </div>
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Alamat Kantor Perusahaan <span class="text-danger">*</span></label>
                        <textarea name="alamat_kantor" rows="2" class="form-control form-control-custom" placeholder="Alamat lengkap kantor pusat perusahaan..." required>{{ $val('alamat_kantor') }}</textarea>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Kabupaten/Kota Kantor <span class="text-danger">*</span></label>
                        <input type="text" name="kota_kantor" class="form-control form-control-custom" value="{{ $val('kota_kantor') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Provinsi Kantor <span class="text-danger">*</span></label>
                        <input type="text" name="provinsi_kantor" class="form-control form-control-custom" value="{{ $val('provinsi_kantor') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Telp/Fax Kantor</label>
                        <input type="text" name="telp_kantor" class="form-control form-control-custom" value="{{ $val('telp_kantor') }}">
                    </div>
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Email / Website Kantor</label>
                        <input type="text" name="email_kantor" class="form-control form-control-custom" value="{{ $val('email_kantor') }}">
                    </div>
                </div>

                <div class="section-divider-title mt-4">
                    <i class="fa-solid fa-industry"></i> IDENTITAS LOKASI PABRIK & OPERASIONAL
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Alamat Pabrik Perusahaan <span class="text-danger">*</span></label>
                        <textarea name="alamat_pabrik" rows="2" class="form-control form-control-custom" placeholder="Alamat lengkap lokasi pabrik pembuatan produk..." required>{{ $val('alamat_pabrik') }}</textarea>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Kabupaten/Kota Pabrik <span class="text-danger">*</span></label>
                        <input type="text" name="kota_pabrik" class="form-control form-control-custom" value="{{ $val('kota_pabrik') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Provinsi Pabrik <span class="text-danger">*</span></label>
                        <input type="text" name="provinsi_pabrik" class="form-control form-control-custom" value="{{ $val('provinsi_pabrik') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Telp/Fax Pabrik</label>
                        <input type="text" name="telp_pabrik" class="form-control form-control-custom" value="{{ $val('telp_pabrik') }}">
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Email / Website Pabrik</label>
                        <input type="text" name="email_pabrik" class="form-control form-control-custom" value="{{ $val('email_pabrik') }}">
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Bahasa yang digunakan di Pabrik <span class="text-danger">*</span></label>
                        <input type="text" name="bahasa_pabrik" class="form-control form-control-custom" value="{{ $val('bahasa_pabrik', 'Indonesia') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Ketersediaan Penerjemah</label>
                        <input type="text" name="penerjemah_pabrik" class="form-control form-control-custom" value="{{ $val('penerjemah_pabrik', 'Tersedia jika diperlukan') }}">
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Jarak Lokasi ke Pabrik (KM) <span class="text-danger">*</span></label>
                        <input type="number" name="jarak_pabrik" class="form-control form-control-custom" value="{{ $val('jarak_pabrik') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Waktu Tempuh <span class="text-danger">*</span></label>
                        <input type="text" name="waktu_pabrik" class="form-control form-control-custom" value="{{ $val('waktu_pabrik') }}" placeholder="Cth: 60 Menit" required>
                    </div>
                </div>

                <div class="section-divider-title mt-4">
                    <i class="fa-solid fa-ship"></i> DATA IMPORTIR / PEMAKLON <span class="text-muted" style="font-size: 11px; text-transform: lowercase;">(opsional)</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nama Importir</label>
                        <input type="text" name="nama_importir" class="form-control form-control-custom" value="{{ $val('nama_importir') }}">
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nomor API Umum</label>
                        <input type="text" name="api_importir" class="form-control form-control-custom" value="{{ $val('api_importir') }}">
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Alamat Importir</label>
                        <input type="text" name="alamat_importir" class="form-control form-control-custom" value="{{ $val('alamat_importir') }}">
                    </div>
                </div>
            </div>

            {{-- STEP 3: SPESIFIKASI PRODUK --}}
            <div class="step-section" data-step="3">
                <div class="section-divider-title">
                    <i class="fa-solid fa-boxes-stacked"></i> III. RUANG LINGKUP & SPESIFIKASI PRODUK
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Nama Komoditas Pupuk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control form-control-custom" value="{{ $val('nama_produk') }}" placeholder="Contoh: Pupuk NPK Padat" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Judul SNI <span class="text-danger">*</span></label>
                        <input type="text" name="judul_sni" class="form-control form-control-custom" value="{{ $val('judul_sni') }}" placeholder="Contoh: Pupuk NPK Padat" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nomor SNI Acuan Rujukan <span class="text-danger">*</span></label>
                        <input type="text" name="no_sni" class="form-control form-control-custom" value="{{ $val('no_sni') }}" placeholder="Contoh: SNI 2803:2012" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Merek Dagang Pupuk <span class="text-danger">*</span></label>
                        <input type="text" name="merek_produk" class="form-control form-control-custom" value="{{ $val('merek_produk') }}" placeholder="Contoh: Mutiara Hijau" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Tipe / Jenis / Warna Pupuk <span class="text-danger">*</span></label>
                        <input type="text" name="tipe_produk" class="form-control form-control-custom" value="{{ $val('tipe_produk') }}" placeholder="Contoh: Granul / Merah" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Asal Pabrik / Negara <span class="text-danger">*</span></label>
                        <input type="text" name="asal_pabrik" class="form-control form-control-custom" value="{{ $val('asal_pabrik') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Status Produk <span class="text-danger">*</span></label>
                        <input type="text" name="status_produk" class="form-control form-control-custom" value="{{ $val('status_produk', 'Produksi Sendiri') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Kapasitas Produksi Tahunan (Ton) <span class="text-danger">*</span></label>
                        <input type="text" name="kapasitas_produksi" class="form-control form-control-custom" value="{{ $val('kapasitas_produksi') }}" required>
                    </div>
                </div>
            </div>

            {{-- STEP 4: SISTEM MUTU & TENAGA KERJA --}}
            <div class="step-section" data-step="4">
                <div class="section-divider-title">
                    <i class="fa-solid fa-award"></i> IV. SISTEM MANAJEMEN MUTU & SNI
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Jumlah Lini Produksi <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_lini" class="form-control form-control-custom" value="{{ $val('jumlah_lini', 1) }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Standar Sistem Manajemen Mutu (SMM) <span class="text-danger">*</span></label>
                        <input type="text" name="standar_smm" class="form-control form-control-custom" value="{{ $val('standar_smm', 'ISO 9001:2015') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Nama Wakil Manajemen Mutu (WMM) <span class="text-danger">*</span></label>
                        <input type="text" name="nama_wmm" class="form-control form-control-custom" value="{{ $val('nama_wmm') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Telepon / Fax WMM</label>
                        <input type="text" name="telp_wmm" class="form-control form-control-custom" value="{{ $val('telp_wmm') }}">
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">HP WMM <span class="text-danger">*</span></label>
                        <input type="text" name="hp_wmm" class="form-control form-control-custom" value="{{ $val('hp_wmm') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Email WMM <span class="text-danger">*</span></label>
                        <input type="email" name="email_wmm" class="form-control form-control-custom" value="{{ $val('email_wmm') }}" required>
                    </div>
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Penjelasan Tahapan Pembubuhan Tanda SNI pada Produksi <span class="text-danger">*</span></label>
                        <textarea name="tahapan_pembubuhan_sni" rows="2" class="form-control form-control-custom" placeholder="Jelaskan di tahap mana cap/logo SNI dicetak pada karung/kemasan..." required>{{ $val('tahapan_pembubuhan_sni') }}</textarea>
                    </div>
                </div>

                <div class="section-divider-title mt-4">
                    <i class="fa-solid fa-users"></i> DATA ALOKASI TENAGA KERJA PERUSAHAAN
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Total Seluruh Tenaga Kerja <span class="text-danger">*</span></label>
                        <input type="number" name="total_tk" class="form-control form-control-custom" value="{{ $val('total_tk') }}" placeholder="Jumlah total staf" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Tenaga Kerja Produksi / Lini <span class="text-danger">*</span></label>
                        <input type="number" name="tk_produksi" class="form-control form-control-custom" value="{{ $val('tk_produksi') }}" required>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Tenaga Kerja Pengendalian Mutu <span class="text-danger">*</span></label>
                        <input type="number" name="tk_mutu" class="form-control form-control-custom" value="{{ $val('tk_mutu') }}" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Tenaga Kerja Tidak Langsung (Staf Kantor) <span class="text-danger">*</span></label>
                        <input type="number" name="tk_staf" class="form-control form-control-custom" value="{{ $val('tk_staf') }}" required>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Tenaga Kerja Bukan Staf (Harian/Outsource) <span class="text-danger">*</span></label>
                        <input type="number" name="tk_nonstaf" class="form-control form-control-custom" value="{{ $val('tk_nonstaf') }}" required>
                    </div>
                </div>
            </div>

            {{-- STEP 5: PERNYATAAN & RINGKASAN --}}
            <div class="step-section" data-step="5">
                <div class="section-divider-title">
                    <i class="fa-solid fa-list-check"></i> V. DOKUMEN PERSYARATAN & RINGKASAN
                </div>

                <div class="bg-light p-3 border rounded mb-4" style="font-size: 13px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkSetuju" required checked>
                        <label class="form-check-label fw-bold text-dark" for="checkSetuju">
                            Saya menyatakan bersedia mematuhi seluruh peraturan LS Pro Pupuk dan tidak akan menyalahgunakan sertifikat produk (Klausul IV).
                        </label>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-3">Tinjauan Isian Data:</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered summary-table">
                        <tbody>
                            <tr>
                                <th>Nama Perusahaan</th>
                                <td id="sum_perusahaan" class="fw-bold text-success">-</td>
                            </tr>
                            <tr>
                                <th>Nama Pemohon</th>
                                <td id="sum_pemohon">-</td>
                            </tr>
                            <tr>
                                <th>Komoditas Produk / SNI</th>
                                <td id="sum_produk">-</td>
                            </tr>
                            <tr>
                                <th>Merek Produk</th>
                                <td id="sum_merek">-</td>
                            </tr>
                            <tr>
                                <th>Kota Pabrik</th>
                                <td id="sum_pabrik">-</td>
                            </tr>
                            <tr>
                                <th>Total Tenaga Kerja</th>
                                <td id="sum_tk">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px; font-size: 14px;">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Dokumen lampiran fisik pendukung lainnya diunggah secara terpisah setelah permohonan diverifikasi oleh Tata Usaha.
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-5">
                <button type="button" class="btn-cancel-gray" id="btnPrev" style="display: none;">
                    <i class="fa-solid fa-arrow-left me-2"></i> Sebelumnya
                </button>
                <a href="{{ route('pengajuan.index') }}" class="btn-cancel-gray" id="btnCancel">
                    <i class="fa-solid fa-xmark me-2"></i> Batal
                </a>
                
                <div>
                    <button type="button" class="btn-submit-green" id="btnNext">
                        Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                    <button type="submit" class="btn-submit-green" id="btnSubmit" style="display: none; background: #0284c7;">
                        Kirim &amp; Ajukan Formulir <i class="fa-solid fa-paper-plane ms-2"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<div id="autosave-toast">
    <i class="fa-solid fa-cloud-arrow-up text-success"></i>
    <span>Menyimpan draft...</span>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 5;
    const form = document.getElementById('formSertifikasi');

    function updateStepUI() {
        // Hide all step sections
        document.querySelectorAll('.step-section').forEach(section => {
            section.classList.remove('active');
        });

        // Show active step section
        document.querySelector(`.step-section[data-step="${currentStep}"]`).classList.add('active');

        // Update stepper items state
        document.querySelectorAll('.wizard-step').forEach(step => {
            const stepNum = parseInt(step.getAttribute('data-step'));
            step.classList.remove('active', 'completed');
            if (stepNum === currentStep) {
                step.classList.add('active');
            } else if (stepNum < currentStep) {
                step.classList.add('completed');
            }
        });

        // Update progress line width
        const progressWidth = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('stepProgress').style.width = `${progressWidth}%`;

        // Update navigation buttons visibility
        if (currentStep === 1) {
            document.getElementById('btnPrev').style.display = 'none';
            document.getElementById('btnCancel').style.display = 'inline-flex';
        } else {
            document.getElementById('btnPrev').style.display = 'inline-flex';
            document.getElementById('btnCancel').style.display = 'none';
        }

        if (currentStep === totalSteps) {
            document.getElementById('btnNext').style.display = 'none';
            document.getElementById('btnSubmit').style.display = 'inline-flex';
            updateSummaryData();
        } else {
            document.getElementById('btnNext').style.display = 'inline-flex';
            document.getElementById('btnSubmit').style.display = 'none';
        }
    }

    function updateSummaryData() {
        document.getElementById('sum_perusahaan').innerText = form.querySelector('[name="nama_perusahaan"]').value || '-';
        document.getElementById('sum_pemohon').innerText = form.querySelector('[name="nama_pemohon"]').value || '-';
        
        const produkName = form.querySelector('[name="nama_produk"]').value || '-';
        const sniVal = form.querySelector('[name="no_sni"]').value || '-';
        document.getElementById('sum_produk').innerHTML = `${produkName} <br><small class="text-muted">(${sniVal})</small>`;
        
        document.getElementById('sum_merek').innerText = form.querySelector('[name="merek_produk"]').value || '-';
        
        const kotaPabrik = form.querySelector('[name="kota_pabrik"]').value || '-';
        const provPabrik = form.querySelector('[name="provinsi_pabrik"]').value || '-';
        document.getElementById('sum_pabrik').innerText = `${kotaPabrik}, ${provPabrik}`;
        
        document.getElementById('sum_tk').innerText = (form.querySelector('[name="total_tk"]').value || '-') + ' Orang';
    }

    function validateStep(step) {
        const activeSection = document.querySelector(`.step-section[data-step="${step}"]`);
        const inputs = activeSection.querySelectorAll('[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            alert('Harap lengkapi semua kolom yang bertanda bintang (*) sebelum melanjutkan.');
        }
        return isValid;
    }

    document.getElementById('btnNext').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            updateStepUI();
            autoSaveDraft();
        }
    });

    document.getElementById('btnPrev').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepUI();
        }
    });

    // Auto save draft via AJAX
    let autoSaveTimer;
    function autoSaveDraft() {
        const formData = new FormData(form);
        formData.append('is_ajax', 1);

        const toast = document.getElementById('autosave-toast');
        toast.querySelector('span').innerText = 'Menyimpan draft...';
        toast.classList.add('show');

        // Post to the store_draft route
        fetch("{{ route('pengajuan.store_draft') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.draft_id) {
                    document.getElementById('draft_id').value = data.draft_id;
                }
                toast.querySelector('span').innerText = 'Draft disimpan otomatis.';
                setTimeout(() => toast.classList.remove('show'), 2000);
            }
        })
        .catch(err => {
            toast.querySelector('span').innerText = 'Gagal menyimpan draft.';
            setTimeout(() => toast.classList.remove('show'), 2000);
        });
    }

    document.getElementById('btnManualSave').addEventListener('click', function() {
        autoSaveDraft();
    });

    // Bind auto save to input change with debounce
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('change', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSaveDraft, 5000);
        });
    });

    // Initial load
    updateStepUI();
</script>
@endsection
