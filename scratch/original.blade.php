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
        font-size: 22px; \r
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
    .custom-card-form {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 25px;
        overflow: hidden;
    }
    .custom-card-header {
        background: #e2e8f0;
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .custom-card-body {
        padding: 20px;
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
    }
    .btn-submit-green:hover {
        background: #15803d;
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
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="form-card-wrapper">
        
        <div class="form-header-title">
            <a href="{{ route('pengajuan.pilih') }}" class="btn-back-green">
                <i class="fa-solid fa-circle-arrow-left"></i>
            </a>
            <div>
                <h3 style="color: #1e293b; font-weight: 700; margin: 0; font-size: 22px;">Formulir Sertifikasi Otomatis</h3>
                <p style="color: #64748b; font-size: 13px; margin: 5px 0 0 0;">Lengkapi isian dokumen kerja berikut untuk digenerate langsung oleh sistem.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px; font-size: 14px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tahap" value="{{ $tahap }}">

            <div class="section-divider-title">
                <i class="fa-solid fa-user-tie"></i> I. Data Pemohon & Perusahaan
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Nama Pemohon / Penanggung Jawab <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pemohon" class="form-control form-control-custom @error('nama_pemohon') is-invalid @enderror" value="{{ old('nama_pemohon') }}" placeholder="Contoh: Budi Santoso">
                    @error('nama_pemohon') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Jabatan dalam Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan_pemohon" class="form-control form-control-custom @error('jabatan_pemohon') is-invalid @enderror" value="{{ old('jabatan_pemohon') }}" placeholder="Contoh: Direktur Utama">
                    @error('jabatan_pemohon') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 d-flex flex-column">
                    <label class="form-label-weight">Alamat Pemohon <span class="text-danger">*</span></label>
                    <textarea name="alamat_pemohon" rows="2" class="form-control form-control-custom @error('alamat_pemohon') is-invalid @enderror" placeholder="Alamat lengkap rumah atau korespondensi pemohon...">{{ old('alamat_pemohon') }}</textarea>
                    @error('alamat_pemohon') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex flex-column">
                    <label class="form-label-weight">Nomor Telepon / Fax <span class="text-danger">*</span></label>
                    <input type="text" name="telp_pemohon" class="form-control form-control-custom @error('telp_pemohon') is-invalid @enderror" value="{{ old('telp_pemohon') }}" placeholder="Contoh: 021-xxxxxx">
                    @error('telp_pemohon') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex flex-column">
                    <label class="form-label-weight">Nomor Handphone (WhatsApp) <span class="text-danger">*</span></label>
                    <input type="text" name="hp_pemohon" class="form-control form-control-custom @error('hp_pemohon') is-invalid @enderror" value="{{ old('hp_pemohon') }}" placeholder="Contoh: 0812xxxxxxxx">
                    @error('hp_pemohon') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex flex-column">
                    <label class="form-label-weight">Kewarganegaraan <span class="text-danger">*</span></label>
                    <input type="text" name="kewarganegaraan_pemohon" class="form-control form-control-custom @error('kewarganegaraan_pemohon') is-invalid @enderror" value="{{ old('kewarganegaraan_pemohon', 'Indonesia') }}">
                    @error('kewarganegaraan_pemohon') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Nama Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_perusahaan" class="form-control form-control-custom @error('nama_perusahaan') is-invalid @enderror" value="{{ old('nama_perusahaan') }}" placeholder="Contoh: PT. Pupuk Subur Makmur">
                    @error('nama_perusahaan') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Nama Penghubung Teknis (CP) <span class="text-danger">*</span></label>
                    <input type="text" name="nama_penghubung" class="form-control form-control-custom @error('nama_penghubung') is-invalid @enderror" value="{{ old('nama_penghubung') }}" placeholder="Nama staff yang mendampingi proses sertifikasi">
                    @error('nama_penghubung') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 d-flex flex-column">
                    <label class="form-label-weight">Alamat Kantor Perusahaan <span class="text-danger">*</span></label>
                    <textarea name="alamat_kantor" rows="2" class="form-control form-control-custom @error('alamat_kantor') is-invalid @enderror" placeholder="Alamat lengkap kantor pusat perusahaan...">{{ old('alamat_kantor') }}</textarea>
                    @error('alamat_kantor') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 d-flex flex-column">
                    <label class="form-label-weight">Alamat Pabrik Perusahaan <span class="text-danger">*</span></label>
                    <textarea name="alamat_pabrik" rows="2" class="form-control form-control-custom @error('alamat_pabrik') is-invalid @enderror" placeholder="Alamat lengkap lokasi pabrik pembuatan produk...">{{ old('alamat_pabrik') }}</textarea>
                    @error('alamat_pabrik') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="section-divider-title">
                <i class="fa-solid fa-boxes-stacked"></i> II. Ruang Lingkup Informasi Produk Pupuk
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Nama Produk Pupuk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_produk" class="form-control form-control-custom @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk') }}" placeholder="Contoh: Pupuk NPK Padat">
                    @error('nama_produk') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Merek Dagang Pupuk <span class="text-danger">*</span></label>
                    <input type="text" name="merek_produk" class="form-control form-control-custom @error('merek_produk') is-invalid @enderror" value="{{ old('merek_produk') }}" placeholder="Contoh: Mutiara Hijau">
                    @error('merek_produk') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Tipe / Jenis / Varian Pupuk <span class="text-danger">*</span></label>
                    <input type="text" name="tipe_produk" class="form-control form-control-custom @error('tipe_produk') is-invalid @enderror" value="{{ old('tipe_produk') }}" placeholder="Contoh: NPK 15-15-15 Granul">
                    @error('tipe_produk') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Nomor SNI Acuan Rujukan <span class="text-danger">*</span></label>
                    <input type="text" name="sni_acuan" class="form-control form-control-custom @error('sni_acuan') is-invalid @enderror" value="{{ old('sni_acuan') }}" placeholder="Contoh: SNI 2803:2012">
                    @error('sni_acuan') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Kapasitas Produksi Tahunan (Ton/Tahun) <span class="text-danger">*</span></label>
                    <input type="text" name="kapasitas_produksi" class="form-control form-control-custom @error('kapasitas_produksi') is-invalid @enderror" value="{{ old('kapasitas_produksi') }}" placeholder="Contoh: 50.000 Ton/Tahun">
                    @error('kapasitas_produksi') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Standar Sistem Manajemen Mutu (SMM) <span class="text-danger">*</span></label>
                    <input type="text" name="standar_smm" class="form-control form-control-custom @error('standar_smm') is-invalid @enderror" value="{{ old('standar_smm', 'ISO 9001:2015') }}">
                    @error('standar_smm') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="section-divider-title">
                <i class="fa-solid fa-users"></i> III. Data Alokasi Tenaga Kerja Perusahaan
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4 d-flex flex-column">
                    <label class="form-label-weight">Total Seluruh Tenaga Kerja <span class="text-danger">*</span></label>
                    <input type="number" name="total_tk" class="form-control form-control-custom @error('total_tk') is-invalid @enderror" value="{{ old('total_tk') }}" placeholder="Jumlah total staff">
                    @error('total_tk') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex flex-column">
                    <label class="form-label-weight">Tenaga Kerja Bidang Produksi / Lini <span class="text-danger">*</span></label>
                    <input type="number" name="tk_produksi" class="form-control form-control-custom @error('tk_produksi') is-invalid @enderror" value="{{ old('tk_produksi') }}" placeholder="Jumlah pekerja pabrik">
                    @error('tk_produksi') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex flex-column">
                    <label class="form-label-weight">Tenaga Kerja Pengendalian Mutu (QC) <span class="text-danger">*</span></label>
                    <input type="number" name="tk_mutu" class="form-control form-control-custom @error('tk_mutu') is-invalid @enderror" value="{{ old('tk_mutu') }}" placeholder="Jumlah tim QC/Laboratorium">
                    @error('tk_mutu') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Tenaga Kerja Tidak Langsung (Staf Kantor) <span class="text-danger">*</span></label>
                    <input type="number" name="tk_staf" class="form-control form-control-custom @error('tk_staf') is-invalid @enderror" value="{{ old('tk_staf') }}" placeholder="Jumlah pegawai bulanan">
                    @error('tk_staf') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-column">
                    <label class="form-label-weight">Tenaga Kerja Bukan Staf (Harian/Outsource) <span class="text-danger">*</span></label>
                    <input type="number" name="tk_nonstaf" class="form-control form-control-custom @error('tk_nonstaf') is-invalid @enderror" value="{{ old('tk_nonstaf') }}" placeholder="Jumlah pegawai harian lepas">
                    @error('tk_nonstaf') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="section-divider-title">
                <i class="fa-solid fa-folder-open"></i> IV. Dokumen Lampiran Persyaratan (Fisik)
            </div>
            <p class="text-muted small mb-3" style="margin-top: -15px;">Silakan unggah dokumen persyaratan di bawah ini. Ekstensi file yang diizinkan: <strong>PDF, DOC, DOCX, JPG, JPEG, PNG</strong> (Maksimal 5MB per file).</p>

            <div class="custom-card-form">
                <div class="custom-card-header">
                    <i class="fa-solid fa-file-shield"></i> Kelompok 1: Legalitas Hukum & Kepemilikan Merek
                </div>
                <div class="custom-card-body row g-3">
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Akte Perusahaan <span class="text-danger">*</span></label>
                        <input type="file" name="akte_perusahaan" class="form-control form-control-custom @error('akte_perusahaan') is-invalid @enderror">
                        @error('akte_perusahaan') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Izin Usaha Industri / Tanda Daftar Industri <span class="text-danger">*</span></label>
                        <input type="file" name="izin_usaha_industri" class="form-control form-control-custom @error('izin_usaha_industri') is-invalid @enderror">
                        @error('izin_usaha_industri') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">SIUP / Tanda Daftar Usaha Perdagangan <span class="text-danger">*</span></label>
                        <input type="file" name="siup_tdup" class="form-control form-control-custom @error('siup_tdup') is-invalid @enderror">
                        @error('siup_tdup') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Sertifikat / Surat Pendaftaran Merek (Ditjen HAKI) <span class="text-danger">*</span></label>
                        <input type="file" name="sertifikat_merek" class="form-control form-control-custom @error('sertifikat_merek') is-invalid @enderror">
                        @error('sertifikat_merek') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Bukti Penunjukan Importir / Kerjasama Agen Agen <span class="text-muted">(Khusus Importir / Opsional)</span></label>
                        <input type="file" name="bukti_importir" class="form-control form-control-custom @error('bukti_importir') is-invalid @enderror">
                        @error('bukti_importir') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="custom-card-form">
                <div class="custom-card-header">
                    <i class="fa-solid fa-sitemap"></i> Kelompok 2: Data Organisasi & Teknis Alur Produksi
                </div>
                <div class="custom-card-body row g-3">
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Struktur Organisasi Perusahaan <span class="text-danger">*</span></label>
                        <input type="file" name="struktur_organisasi" class="form-control form-control-custom @error('struktur_organisasi') is-invalid @enderror">
                        @error('struktur_organisasi') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Alur Proses Produksi & Pengendalian Mutu <span class="text-danger">*</span></label>
                        <input type="file" name="alur_produksi_mutu" class="form-control form-control-custom @error('alur_produksi_mutu') is-invalid @enderror">
                        @error('alur_produksi_mutu') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Daftar Alat dan Mesin Produksi <span class="text-danger">*</span></label>
                        <input type="file" name="daftar_alat_mesin" class="form-control form-control-custom @error('daftar_alat_mesin') is-invalid @enderror">
                        @error('daftar_alat_mesin') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Daftar Material Kritis <span class="text-danger">*</span></label>
                        <input type="file" name="daftar_material_kritis" class="form-control form-control-custom @error('daftar_material_kritis') is-invalid @enderror">
                        @error('daftar_material_kritis') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label-weight">Daftar Peralatan Pengujian Terkalibrasi <span class="text-danger">*</span></label>
                        <input type="file" name="daftar_alat_uji_kalibrasi" class="form-control form-control-custom @error('daftar_alat_uji_kalibrasi') is-invalid @enderror">
                        @error('daftar_alat_uji_kalibrasi') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="custom-card-form">
                <div class="custom-card-header">
                    <i class="fa-solid fa-award"></i> Kelompok 3: Dokumentasi Sistem Mutu & Label SNI
                </div>
                <div class="custom-card-body row g-3">
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Pedoman Mutu SMM Perusahaan <span class="text-danger">*</span></label>
                        <input type="file" name="pedoman_mutu" class="form-control form-control-custom @error('pedoman_mutu') is-invalid @enderror">
                        @error('pedoman_mutu') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label-weight">Daftar Prosedur, Instruksi Kerja, & Formulir <span class="text-danger">*</span></label>
                        <input type="file" name="daftar_prosedur_ik" class="form-control form-control-custom @error('daftar_prosedur_ik') is-invalid @enderror">
                        @error('daftar_prosedur_ik') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12 d-flex flex-column">
                        <label class="form-label-weight">Ilustrasi & Tata Cara Pembubuhan Tanda SNI (Contoh Penandaan Label) <span class="text-danger">*</span></label>
                        <input type="file" name="ilustrasi_tanda_sni" class="form-control form-control-custom @error('ilustrasi_tanda_sni') is-invalid @enderror">
                        @error('ilustrasi_tanda_sni') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="custom-card-form">
                <div class="custom-card-header" style="background: #22c55e; color: white;">
                    <i class="fa-solid fa-file-image" style="margin-right: 6px;"></i> LAMPIRAN KOMPONEN FISIK
                </div>
                <div class="custom-card-body">
                    <div class="d-flex flex-column">
                        <label class="form-label-weight">Unggah Foto Sampel Produk Fisik <span class="text-danger">*</span></label>
                        <input type="file" name="foto_produk" class="form-control form-control-custom @error('foto_produk') is-invalid @enderror">
                        @error('foto_produk') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-4">
                <a href="{{ route('pengajuan.pilih') }}" class="btn-cancel-gray">
                    <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Kembali
                </a>
                <div style="display: flex; gap: 10px;">
                    <button type="reset" class="btn btn-outline-danger" style="border-radius: 10px; padding: 10px 20px; font-size: 14px; font-weight: 600;">Reset</button>
                    <button type="submit" class="btn-submit-green">
                        Kirim  <i class="fa-solid fa-paper-plane" style="margin-left: 6px;"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
