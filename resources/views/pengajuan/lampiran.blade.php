@extends('layouts.app')

@section('title', 'Upload Lampiran Pengajuan')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">Upload Lampiran Pengajuan</h3>
                    <p class="text-muted mb-0">
                        Verifikasi Tata Usaha sudah selesai. Lengkapi lampiran pendukung berikut bila diperlukan untuk tahap perjanjian.
                    </p>
                </div>
                <a href="{{ route('aktivitas.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali lampiran:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info border-0" style="border-radius: 12px;">
                Format yang diterima: PDF, JPG, PNG, DOC, DOCX. Ukuran maksimal 50 MB per file.
            </div>

            <form action="{{ route('pengajuan.lampiran.store', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    @foreach ($lampiranFields as $field => $meta)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ $meta['label'] }}
                                @if (!empty($formData[$field]))
                                    <span class="badge bg-success-subtle text-success ms-2 border border-success-subtle px-2 py-1" style="font-size: 10px; border-radius: 6px;">
                                        <i class="fa-solid fa-check-circle me-1"></i>Tersimpan
                                    </span>
                                @elseif ($meta['required'])
                                    <span class="text-danger">*</span>
                                @else
                                    <span class="text-muted small ms-1">(Opsional)</span>
                                @endif
                            </label>

                            @if (!empty($formData[$field]))
                                <!-- File sudah ada -->
                                <div id="view-mode-{{ $field }}" class="d-flex align-items-center justify-content-between border rounded p-2 bg-light">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden" style="max-width: 70%;">
                                        <i class="fa-solid fa-file-lines text-primary fs-5"></i>
                                        <span class="text-truncate fw-medium" style="font-size: 13px;" title="{{ $formData[$field] }}">
                                            {{ strlen($formData[$field]) > 35 ? substr($formData[$field], 0, 35) . '...' : $formData[$field] }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ asset('storage/permohonan/lampiran/' . $formData[$field]) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Lihat Dokumen" style="padding: 0.25rem 0.5rem; border-radius: 6px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Ganti Dokumen" style="padding: 0.25rem 0.5rem; border-radius: 6px;" 
                                                onclick="document.getElementById('view-mode-{{ $field }}').classList.add('d-none'); document.getElementById('edit-mode-{{ $field }}').classList.remove('d-none'); document.getElementById('input-{{ $field }}').required = {{ $meta['required'] ? 'true' : 'false' }};">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Input hidden (muncul saat klik X) -->
                                <div id="edit-mode-{{ $field }}" class="d-none">
                                    <input
                                        type="file"
                                        id="input-{{ $field }}"
                                        name="{{ $field }}"
                                        class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <div class="form-text text-muted mt-1" style="font-size: 11px;">
                                        <i class="fa-solid fa-info-circle"></i> Pilih dokumen baru untuk mengganti file sebelumnya.
                                    </div>
                                </div>
                            @else
                                <!-- Belum ada file -->
                                <input
                                    type="file"
                                    name="{{ $field }}"
                                    class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                    {{ $meta['required'] ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end gap-2 border-top mt-4 pt-4">
                    <button type="submit" name="action" value="draft" class="btn btn-outline-success fw-semibold" formnovalidate>
                        Simpan Draft
                    </button>
                    <a href="{{ route('aktivitas.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" name="action" value="submit" class="btn btn-success fw-semibold">
                        Simpan Lampiran & Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
