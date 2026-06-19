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
        cursor: pointer;
    }
    .btn-cancel-gray:hover {
        background: #f8fafc;
        color: #334155;
    }
    
    /* Stepper Styling */
    .wizard-stepper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        position: relative;
    }
    .wizard-stepper::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .wizard-stepper-progress {
        position: absolute;
        top: 20px;
        left: 0;
        height: 3px;
        background: #16a34a;
        z-index: 2;
        transition: width 0.3s ease;
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
        transition: all 0.3s;
    }
    .wizard-step.active .wizard-step-circle {
        border-color: #16a34a;
        background: white;
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
        color: #16a34a;
    }
    
    /* Inner Nav Tabs */
    .inner-nav-tabs {
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .inner-nav-link {
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 5px;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: 0.2s;
    }
    .inner-nav-link:hover {
        color: #16a34a;
    }
    .inner-nav-link.active {
        color: #16a34a;
        border-bottom-color: #16a34a;
    }
    .inner-tab-pane {
        display: none;
    }
    .inner-tab-pane.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .link-lanjut {
        color: #16a34a;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
    }
    .link-lanjut:hover {
        text-decoration: underline;
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
    
    $stepsData = [
        1 => 'Pilih Produk',
        2 => 'Data Pemohon',
        3 => 'Data Pupuk',
        4 => 'Tenaga Kerja',
        5 => 'Pernyataan',
        6 => 'Review & Submit',
    ];
    
    // Calculate progress line width
    $progressWidth = (($step - 1) / (count($stepsData) - 1)) * 100;
@endphp

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="color: #0f172a; font-weight: 800; margin: 0; font-size: 24px;">Pengajuan Sertifikasi Baru</h3>
            <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0;">Lengkapi setiap langkah untuk menyelesaikan pengajuan</p>
        </div>
        <button type="button" class="btn btn-outline-secondary rounded-3 px-3 py-2 bg-white" onclick="manualSaveDraft()" style="font-size: 14px; font-weight: 500; border-color: #cbd5e1;">
            <i class="fa-regular fa-floppy-disk me-2"></i> Simpan Draft
        </button>
    </div>

    <div class="form-card-wrapper mb-4" style="padding: 25px 35px 20px 35px;">
        {{-- Stepper UI --}}
        <div class="wizard-stepper">
            <div class="wizard-stepper-progress" style="width: {{ $progressWidth }}%;"></div>
            @foreach($stepsData as $idx => $label)
                <div class="wizard-step {{ $step == $idx ? 'active' : '' }} {{ $step > $idx ? 'completed' : '' }}">
                    <div class="wizard-step-circle">
                        @if($step > $idx)
                            <i class="fa-solid fa-check"></i>
                        @else
                            {{ $idx }}
                        @endif
                    </div>
                    <div class="wizard-step-label">{{ $label }}</div>
                </div>
            @endforeach
        </div>
        <div class="text-muted mt-2 border-top pt-3" style="font-size: 13px;">
            Langkah {{ $step }} dari 6
        </div>
    </div>

    <div class="form-card-wrapper">
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px; font-size: 14px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Form Area --}}
        <form action="{{ $step == 6 ? route('pengajuan.wizard.submit') : route('pengajuan.wizard.save') }}" method="POST" id="wizardForm">
            @csrf
            <input type="hidden" name="draft_id" value="{{ $draft->id }}">
            <input type="hidden" name="current_step" value="{{ $step }}">
            <input type="hidden" name="progress_percent" value="{{ round($progressWidth) }}">
            <input type="hidden" name="next_step" value="{{ $step < 6 ? $step + 1 : 6 }}">
            
            <div id="step-content">
                @if($step == 1)
                    <h5 class="fw-bold text-dark mb-4 pb-2">Pilih Produk</h5>
                    <div class="text-muted mb-4">
                        <i class="fa-solid fa-circle-check text-success fs-2 mb-3"></i><br>
                        Langkah ini telah dilalui. Anda sedang mengajukan {{ $draft->jenis_sertifikasi ?? 'Sertifikasi Baru' }}.
                    </div>
                @elseif($step == 2)
                    {{-- Inner Nav Tabs --}}
                    <div class="inner-nav-tabs">
                        <div class="inner-nav-link active" onclick="switchInnerTab('pemohon', this)">Data Pemohon</div>
                        <div class="inner-nav-link" onclick="switchInnerTab('korespondensi', this)">Korespondensi</div>
                        <div class="inner-nav-link" onclick="switchInnerTab('perusahaan', this)">Data Perusahaan</div>
                        <div class="inner-nav-link" onclick="switchInnerTab('legalitas', this)">Legalitas Pabrik</div>
                        <div class="inner-nav-link" onclick="switchInnerTab('pemaklon', this)">Pemaklon / Importir</div>
                        <div class="inner-nav-link" onclick="switchInnerTab('lainnya', this)">Data Lainnya</div>
                    </div>
                    
                    {{-- Tab 1: Data Pemohon --}}
                    <div class="inner-tab-pane active" id="tab-pemohon">
                        <h6 class="fw-bold text-dark mb-4">Data Pemohon</h6>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Nama Pemohon</label>
                                <input type="text" name="nama_pemohon" class="form-control form-control-custom" value="{{ $val('nama_pemohon') }}" placeholder="PT / CV / UD ...">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Nama PIC</label>
                                <input type="text" name="nama_penghubung" class="form-control form-control-custom" value="{{ $val('nama_penghubung') }}" placeholder="Nama lengkap">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Jabatan PIC</label>
                                <input type="text" name="jabatan_penghubung" class="form-control form-control-custom" value="{{ $val('jabatan_penghubung') }}" placeholder="Jabatan / posisi">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Nomor Telepon</label>
                                <input type="text" name="hp_penghubung" class="form-control form-control-custom" value="{{ $val('hp_penghubung') }}" placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Email</label>
                                <input type="email" name="email_penghubung" class="form-control form-control-custom" value="{{ $val('email_penghubung') }}" placeholder="email@perusahaan.com">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">NPWP</label>
                                <input type="text" name="npwp_perusahaan" class="form-control form-control-custom" value="{{ $val('npwp_perusahaan') }}" placeholder="XX.XXX.XXX.X-XXX.XXX">
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-2">
                            <a class="link-lanjut" onclick="switchInnerTab('korespondensi', document.querySelectorAll('.inner-nav-link')[1])">Lanjut ke Korespondensi <i class="fa-solid fa-angle-right ms-1"></i></a>
                        </div>
                    </div>

                    {{-- Tab 2: Korespondensi --}}
                    <div class="inner-tab-pane" id="tab-korespondensi">
                        <h6 class="fw-bold text-dark mb-4">Korespondensi</h6>
                        <div class="row g-4">
                            <div class="col-md-12 d-flex flex-column">
                                <label class="form-label-weight">Alamat Pemohon</label>
                                <textarea name="alamat_pemohon" rows="2" class="form-control form-control-custom" placeholder="Alamat lengkap rumah pemohon...">{{ $val('alamat_pemohon') }}</textarea>
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Nomor Telepon / Fax Pemohon</label>
                                <input type="text" name="telp_pemohon" class="form-control form-control-custom" value="{{ $val('telp_pemohon') }}">
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Kewarganegaraan</label>
                                <input type="text" name="kewarganegaraan_pemohon" class="form-control form-control-custom" value="{{ $val('kewarganegaraan_pemohon', 'Indonesia') }}">
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Status Pemohon</label>
                                <select name="status_pemohon" class="form-control form-control-custom">
                                    <option value="Produsen" {{ $val('status_pemohon') == 'Produsen' ? 'selected' : '' }}>Produsen Dalam Negeri</option>
                                    <option value="Importir/Agen" {{ $val('status_pemohon') == 'Importir/Agen' ? 'selected' : '' }}>Importir / Agen</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 pt-2">
                            <a class="link-lanjut" onclick="switchInnerTab('perusahaan', document.querySelectorAll('.inner-nav-link')[2])">Lanjut ke Data Perusahaan <i class="fa-solid fa-angle-right ms-1"></i></a>
                        </div>
                    </div>

                    {{-- Tab 3: Data Perusahaan --}}
                    <div class="inner-tab-pane" id="tab-perusahaan">
                        <h6 class="fw-bold text-dark mb-4">Data Perusahaan</h6>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Nama Perusahaan (Sesuai Akta)</label>
                                <input type="text" name="nama_perusahaan" class="form-control form-control-custom" value="{{ $val('nama_perusahaan') }}">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Bentuk Badan Hukum</label>
                                <input type="text" name="badan_hukum" class="form-control form-control-custom" value="{{ $val('badan_hukum') }}">
                            </div>
                            <div class="col-md-12 d-flex flex-column">
                                <label class="form-label-weight">Alamat Kantor Perusahaan</label>
                                <textarea name="alamat_kantor" rows="2" class="form-control form-control-custom">{{ $val('alamat_kantor') }}</textarea>
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Kabupaten/Kota Kantor</label>
                                <input type="text" name="kota_kantor" class="form-control form-control-custom" value="{{ $val('kota_kantor') }}">
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Provinsi Kantor</label>
                                <input type="text" name="provinsi_kantor" class="form-control form-control-custom" value="{{ $val('provinsi_kantor') }}">
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Telp/Fax Kantor</label>
                                <input type="text" name="telp_kantor" class="form-control form-control-custom" value="{{ $val('telp_kantor') }}">
                            </div>
                        </div>
                        <div class="mt-4 pt-2">
                            <a class="link-lanjut" onclick="switchInnerTab('legalitas', document.querySelectorAll('.inner-nav-link')[3])">Lanjut ke Legalitas Pabrik <i class="fa-solid fa-angle-right ms-1"></i></a>
                        </div>
                    </div>

                    {{-- Tab 4: Legalitas Pabrik --}}
                    <div class="inner-tab-pane" id="tab-legalitas">
                        <h6 class="fw-bold text-dark mb-4">Legalitas Pabrik</h6>
                        <div class="row g-4">
                            <div class="col-md-12 d-flex flex-column">
                                <label class="form-label-weight">Alamat Pabrik Perusahaan</label>
                                <textarea name="alamat_pabrik" rows="2" class="form-control form-control-custom">{{ $val('alamat_pabrik') }}</textarea>
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Kabupaten/Kota Pabrik</label>
                                <input type="text" name="kota_pabrik" class="form-control form-control-custom" value="{{ $val('kota_pabrik') }}">
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Provinsi Pabrik</label>
                                <input type="text" name="provinsi_pabrik" class="form-control form-control-custom" value="{{ $val('provinsi_pabrik') }}">
                            </div>
                            <div class="col-md-4 d-flex flex-column">
                                <label class="form-label-weight">Telp/Fax Pabrik</label>
                                <input type="text" name="telp_pabrik" class="form-control form-control-custom" value="{{ $val('telp_pabrik') }}">
                            </div>
                        </div>
                        <div class="mt-4 pt-2">
                            <a class="link-lanjut" onclick="switchInnerTab('pemaklon', document.querySelectorAll('.inner-nav-link')[4])">Lanjut ke Pemaklon / Importir <i class="fa-solid fa-angle-right ms-1"></i></a>
                        </div>
                    </div>
                    
                    {{-- Tab 5: Pemaklon / Importir --}}
                    <div class="inner-tab-pane" id="tab-pemaklon">
                        <h6 class="fw-bold text-dark mb-4">Data Importir / Pemaklon <span class="text-muted" style="font-size: 13px; font-weight: 500;">(Opsional)</span></h6>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Nama Importir</label>
                                <input type="text" name="nama_importir" class="form-control form-control-custom" value="{{ $val('nama_importir') }}">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Nomor API Umum</label>
                                <input type="text" name="api_importir" class="form-control form-control-custom" value="{{ $val('api_importir') }}">
                            </div>
                            <div class="col-md-12 d-flex flex-column">
                                <label class="form-label-weight">Alamat Importir</label>
                                <input type="text" name="alamat_importir" class="form-control form-control-custom" value="{{ $val('alamat_importir') }}">
                            </div>
                        </div>
                        <div class="mt-4 pt-2">
                            <a class="link-lanjut" onclick="switchInnerTab('lainnya', document.querySelectorAll('.inner-nav-link')[5])">Lanjut ke Data Lainnya <i class="fa-solid fa-angle-right ms-1"></i></a>
                        </div>
                    </div>
                    
                    {{-- Tab 6: Data Lainnya --}}
                    <div class="inner-tab-pane" id="tab-lainnya">
                        <h6 class="fw-bold text-dark mb-4">Data Lainnya</h6>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Bahasa yang digunakan di Pabrik</label>
                                <input type="text" name="bahasa_pabrik" class="form-control form-control-custom" value="{{ $val('bahasa_pabrik', 'Indonesia') }}">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Ketersediaan Penerjemah</label>
                                <input type="text" name="penerjemah_pabrik" class="form-control form-control-custom" value="{{ $val('penerjemah_pabrik', 'Tersedia jika diperlukan') }}">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Jarak Lokasi ke Pabrik (KM)</label>
                                <input type="number" name="jarak_pabrik" class="form-control form-control-custom" value="{{ $val('jarak_pabrik') }}">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <label class="form-label-weight">Waktu Tempuh</label>
                                <input type="text" name="waktu_pabrik" class="form-control form-control-custom" value="{{ $val('waktu_pabrik') }}">
                            </div>
                        </div>
                    </div>

                @elseif($step == 3)
                    {{-- STEP 3: Data Pupuk --}}
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Data Pupuk & Produk</h5>
                    <div class="row g-4">
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Nama Komoditas Pupuk</label>
                            <input type="text" name="nama_produk" class="form-control form-control-custom" value="{{ $val('nama_produk') }}" placeholder="Contoh: Pupuk NPK Padat">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Judul SNI</label>
                            <input type="text" name="judul_sni" class="form-control form-control-custom" value="{{ $val('judul_sni') }}">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Nomor SNI Acuan Rujukan</label>
                            <input type="text" name="no_sni" class="form-control form-control-custom" value="{{ $val('no_sni') }}" placeholder="Contoh: SNI 2803:2012">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Merek Dagang Pupuk</label>
                            <input type="text" name="merek_produk" class="form-control form-control-custom" value="{{ $val('merek_produk') }}" placeholder="Contoh: Mutiara Hijau">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Tipe / Jenis / Warna Pupuk</label>
                            <input type="text" name="tipe_produk" class="form-control form-control-custom" value="{{ $val('tipe_produk') }}" placeholder="Contoh: Granul / Merah">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Asal Pabrik / Negara</label>
                            <input type="text" name="asal_pabrik" class="form-control form-control-custom" value="{{ $val('asal_pabrik', 'Indonesia') }}">
                        </div>
                        <div class="col-md-12 d-flex flex-column">
                            <label class="form-label-weight">Status Produk</label>
                            <input type="text" name="status_produk" class="form-control form-control-custom" value="{{ $val('status_produk', 'Produksi Sendiri') }}">
                        </div>
                    </div>

                @elseif($step == 4)
                    {{-- STEP 4: Tenaga Kerja --}}
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Tenaga Kerja & Sistem Mutu</h5>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4 d-flex flex-column">
                            <label class="form-label-weight">Jumlah Lini Produksi</label>
                            <input type="number" name="jumlah_lini" class="form-control form-control-custom" value="{{ $val('jumlah_lini', 1) }}">
                        </div>
                        <div class="col-md-4 d-flex flex-column">
                            <label class="form-label-weight">Kapasitas Produksi Tahunan (Ton)</label>
                            <input type="text" name="kapasitas_produksi" class="form-control form-control-custom" value="{{ $val('kapasitas_produksi') }}">
                        </div>
                        <div class="col-md-4 d-flex flex-column">
                            <label class="form-label-weight">Standar SMM</label>
                            <input type="text" name="standar_smm" class="form-control form-control-custom" value="{{ $val('standar_smm', 'ISO 9001:2015') }}">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Nama Wakil Manajemen Mutu (WMM)</label>
                            <input type="text" name="nama_wmm" class="form-control form-control-custom" value="{{ $val('nama_wmm') }}">
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <label class="form-label-weight">Email WMM</label>
                            <input type="email" name="email_wmm" class="form-control form-control-custom" value="{{ $val('email_wmm') }}">
                        </div>
                    </div>
                    
                    <h6 class="fw-bold text-dark mb-4">Alokasi Tenaga Kerja</h6>
                    <div class="row g-4">
                        <div class="col-md-4 d-flex flex-column">
                            <label class="form-label-weight">Total Seluruh Tenaga Kerja</label>
                            <input type="number" name="total_tk" class="form-control form-control-custom" value="{{ $val('total_tk') }}">
                        </div>
                        <div class="col-md-4 d-flex flex-column">
                            <label class="form-label-weight">Tenaga Kerja Produksi</label>
                            <input type="number" name="tk_produksi" class="form-control form-control-custom" value="{{ $val('tk_produksi') }}">
                        </div>
                        <div class="col-md-4 d-flex flex-column">
                            <label class="form-label-weight">Tenaga Kerja Pengendalian Mutu</label>
                            <input type="number" name="tk_mutu" class="form-control form-control-custom" value="{{ $val('tk_mutu') }}">
                        </div>
                    </div>

                @elseif($step == 5)
                    {{-- STEP 5: Pernyataan --}}
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Pernyataan</h5>
                    <div class="bg-light p-4 border rounded-4 mb-4" style="font-size: 14px; line-height: 1.6; color: #334155;">
                        <p class="mb-3">Dengan ini, kami menyatakan bahwa:</p>
                        <ol class="mb-0">
                            <li class="mb-2">Semua data dan informasi yang diberikan dalam formulir ini adalah benar, akurat, dan dapat dipertanggungjawabkan.</li>
                            <li class="mb-2">Kami bersedia mematuhi seluruh persyaratan sertifikasi SNI yang ditetapkan oleh LSPro Pupuk dan peraturan perundang-undangan yang berlaku.</li>
                            <li class="mb-2">Kami bersedia menerima tim auditor LSPro untuk melakukan audit kecukupan dokumen, audit lapangan, dan pengambilan sampel pada fasilitas produksi kami.</li>
                            <li>Kami tidak akan menyalahgunakan sertifikat produk maupun tanda SNI (Klausul IV).</li>
                        </ol>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="checkSetuju" name="pernyataan_setuju" required {{ $val('pernyataan_setuju') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark ms-2" for="checkSetuju" style="cursor: pointer;">
                            Saya menyetujui seluruh pernyataan di atas.
                        </label>
                    </div>

                @elseif($step == 6)
                    {{-- STEP 6: Review & Submit --}}
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Review & Submit</h5>
                    <p class="text-muted mb-4">Harap periksa kembali kesesuaian data yang telah Anda isi sebelum mengirimkan formulir permohonan. Anda masih dapat kembali ke langkah sebelumnya jika ada data yang ingin diubah.</p>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered summary-table" style="border-radius: 8px; overflow: hidden;">
                            <tbody>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <td class="fw-bold text-dark">{{ $val('nama_perusahaan', '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Merek Produk</th>
                                    <td>{{ $val('merek_produk', '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Komoditas (SNI Acuan)</th>
                                    <td>{{ $val('nama_produk', '-') }} <br><span class="text-muted" style="font-size:12px;">{{ $val('no_sni', '-') }}</span></td>
                                </tr>
                                <tr>
                                    <th>Nama Penghubung (CP)</th>
                                    <td>{{ $val('nama_penghubung', '-') }} ({{ $val('hp_penghubung', '-') }})</td>
                                </tr>
                                <tr>
                                    <th>Pabrik</th>
                                    <td>{{ $val('kota_pabrik', '-') }}, {{ $val('provinsi_pabrik', '-') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 pt-4" style="border-top: 1px solid #e2e8f0;">
                <div>
                    @if($step > 1)
                        <button type="submit" name="next_step" value="{{ $step - 1 }}" class="btn-cancel-gray">
                            <i class="fa-solid fa-angle-left me-2"></i> Kembali
                        </button>
                    @endif
                </div>
                
                <div>
                    @if($step < 6)
                        <button type="submit" class="btn-submit-green">
                            Lanjut <i class="fa-solid fa-angle-right ms-2"></i>
                        </button>
                    @else
                        <button type="submit" class="btn-submit-green" style="background: #0ea5e9; border-radius: 30px; padding: 12px 35px;">
                            <i class="fa-solid fa-paper-plane me-2"></i> Ajukan Formulir
                        </button>
                    @endif
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
    const form = document.getElementById('wizardForm');
    let autoSaveTimer;
    let isSubmitting = false;

    // Inner Tabs Logic
    function switchInnerTab(tabId, element) {
        document.querySelectorAll('.inner-tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.inner-nav-link').forEach(el => el.classList.remove('active'));
        
        document.getElementById('tab-' + tabId).classList.add('active');
        element.classList.add('active');
        
        // Scroll slightly up if needed
        window.scrollBy({ top: -50, behavior: 'smooth' });
    }

    // Auto save functionality
    function autoSaveDraft() {
        if(isSubmitting) return;

        const formData = new FormData(form);
        formData.append('is_ajax', 1);

        const toast = document.getElementById('autosave-toast');
        toast.querySelector('span').innerText = 'Menyimpan draft otomatis...';
        toast.classList.add('show');

        fetch("{{ route('pengajuan.wizard.save') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => response.json())
          .then(data => {
              if(data.success) {
                  toast.querySelector('span').innerText = 'Draft berhasil tersimpan.';
                  setTimeout(() => toast.classList.remove('show'), 2000);
              }
          }).catch(err => {
              toast.querySelector('span').innerText = 'Gagal menyimpan draft.';
              setTimeout(() => toast.classList.remove('show'), 2000);
          });
    }

    function manualSaveDraft() {
        autoSaveDraft();
    }

    // Bind auto save to input changes (debounced 5 seconds to avoid spam)
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('change', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSaveDraft, 5000);
        });
    });

    // Also auto save every 30 seconds globally
    setInterval(() => {
        if(!isSubmitting) autoSaveDraft();
    }, 30000);

    form.addEventListener('submit', () => {
        isSubmitting = true;
    });
</script>
@endsection
