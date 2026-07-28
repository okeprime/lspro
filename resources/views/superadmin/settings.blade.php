@extends('layouts.app')
@section('title', 'Pengaturan Sistem - Superadmin')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="mb-4">
        <h2 style="color: #1e293b; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 8px;">Pengaturan Sistem</h2>
        <p class="text-muted" style="font-size: 15px;">Konfigurasi parameter dasar dan perbarui template dokumen sistem.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 500; font-size: 14px; background-color: #dcfce7; border-color: #bbf7d0; color: #166534;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 500; font-size: 14px; background-color: #fee2e2; border-color: #fecaca; color: #991b1b;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Pengaturan Kebijakan Sistem -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold" style="color: #334155;">
                        <i class="fa-solid fa-scale-balanced me-2" style="color: #16a34a;"></i> Kebijakan Sistem & Mutu
                    </h5>
                    <p class="text-muted small">Tentukan kebijakan dasar sistem sertifikasi LSPro BRMP SDLP.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('superadmin.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569;">Kebijakan Umum</label>
                            <textarea name="kebijakan_umum" class="form-control form-control-custom" rows="6" required style="border-radius: 12px; resize: none;">{{ $settings['kebijakan_umum'] ?? 'Kebijakan dasar sistem sertifikasi LSPro BRMP SDLP.' }}</textarea>
                            <small class="text-muted mt-2 d-block" style="font-size: 12px;">Kebijakan ini akan menjadi panduan bagi semua pihak yang terkait dalam sistem sertifikasi.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100" style="background-color: #16a34a; border-color: #16a34a; border-radius: 10px; font-weight: 600;">
                            <i class="fa-solid fa-save me-2"></i> Simpan Kebijakan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Unggah Template Dokumen -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold" style="color: #334155;">
                        <i class="fa-solid fa-file-word me-2" style="color: #2563eb;"></i> Template Formulir Keseluruhan
                    </h5>
                    <p class="text-muted small">Perbarui template formulir yang digunakan di sistem.</p>
                </div>
                <div class="card-body p-4">
                    @foreach($templateStatus as $key => $status)
                    <div class="mb-3">
                        @if($status['exists'])
                            <div class="alert mb-0 p-2" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-size: 12px;">
                                <i class="fa-solid fa-circle-check me-1"></i> <strong>{{ ucfirst($key) }}:</strong> {{ $status['filename'] }}
                                <span style="font-size: 11px; color: #15803d; display: block;">Terakhir: {{ date('d M Y H:i', $status['updated_at']) }}</span>
                            </div>
                        @else
                            <div class="alert mb-0 p-2" style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b; font-size: 12px;">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> Template <strong>{{ ucfirst($key) }}</strong> belum tersedia.
                            </div>
                        @endif
                    </div>
                    @endforeach

                    <form action="{{ route('superadmin.settings.upload_template') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569;">Jenis Template Dokumen</label>
                            <select name="jenis_dokumen" class="form-select form-select-custom" required>
                                <option value="permohonan">Formulir Permohonan (.docx)</option>
                                <option value="tinjauan">Formulir Tinjauan (.docx)</option>
                                <option value="kebijakan">Manual Mutu / Kebijakan (.pdf)</option>
                            </select>
                        </div>
                        
                        <div class="mb-4 mt-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569;">Pilih File Template Baru (.docx / .pdf)</label>
                            <input type="file" name="template_dokumen" class="form-control form-control-custom" accept=".docx,.pdf" required style="padding: 10px; border: 2px dashed #cbd5e1; background: #f8fafc;">
                            <small class="text-muted d-block mt-2" style="font-size: 12px;">Pastikan tipe file sesuai dengan jenis formulir. Maksimal ukuran file: 5MB.</small>
                            @error('template_dokumen')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100" style="background-color: #2563eb; border-color: #2563eb; border-radius: 10px; font-weight: 600;">
                            <i class="fa-solid fa-upload me-2"></i> Unggah dan Timpa Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
