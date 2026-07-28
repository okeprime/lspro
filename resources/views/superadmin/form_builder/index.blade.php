@extends('layouts.app')

@section('title', 'Pengaturan Sistem — Kelola Formulir & Template')

@section('extra-css')
<style>
    .fb-landing-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        color: white;
    }
    .fb-landing-header h2 { font-size: 24px; font-weight: 800; margin: 0 0 6px 0; }
    .fb-landing-header p  { font-size: 14px; opacity: 0.8; margin: 0; }

    .form-type-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 28px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        display: block;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .form-type-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        border-radius: 16px 0 0 16px;
        transition: width 0.3s;
    }
    .form-type-card:hover {
        border-color: transparent;
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        transform: translateY(-4px);
    }
    .form-type-card:hover::before {
        width: 6px;
    }
    .form-type-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        color: white;
        margin-bottom: 18px;
    }
    .form-type-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
        min-height: 40px;
    }
    .form-type-desc {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
        margin-bottom: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .form-type-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .form-type-count {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .form-type-action {
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="fb-landing-header">
        <h2><i class="fa-solid fa-cog me-2" style="opacity:0.8;"></i>Pengaturan Sistem & Template</h2>
        <p>Pilih formulir yang ingin Anda kelola. Anda dapat mengatur pertanyaan dinamis (Form Builder) dan memperbarui template dokumen (.docx) untuk masing-masing form.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        @foreach($formTypes as $typeKey => $meta)
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('superadmin.form_builder.index', ['type' => $typeKey]) }}" class="form-type-card">
                    <div style="position: absolute; top:0; left:0; width:4px; height:100%; background: {{ $meta['color'] }}; border-radius: 16px 0 0 16px;"></div>
                    <div class="form-type-icon" style="background: {{ $meta['color'] }};">
                        <i class="{{ $meta['icon'] }}"></i>
                    </div>
                    <div class="form-type-title">{{ $meta['title'] }}</div>
                    <div class="form-type-desc">{{ $meta['description'] }}</div>
                    <div class="form-type-footer">
                        <span class="form-type-count">
                            <i class="fa-solid fa-list me-1"></i>
                            {{ $counts[$typeKey] ?? 0 }} Pertanyaan
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-light border" onclick="event.preventDefault(); openUploadModal('{{ $typeKey }}', '{{ $meta['title'] }}', '{{ $meta['filename'] }}')" style="font-size: 11px; font-weight: 600;" title="Upload Template .docx">
                                <i class="fa-solid fa-upload"></i>
                            </button>
                            <span class="form-type-action btn btn-sm text-white" style="background: {{ $meta['color'] }}; font-size: 11px; border-radius: 6px;">
                                Kelola <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Info --}}
    <div class="card border-0 shadow-sm rounded-4 mt-4 p-4" style="background: #f8fafc;">
        <div class="d-flex align-items-start gap-3">
            <div style="background: #eff6ff; border-radius: 12px; width: 42px; height: 42px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fa-solid fa-circle-info text-primary" style="font-size: 18px;"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Panduan Pengaturan Sistem</h6>
                <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.7;">
                    1. Klik tombol <strong>Kelola</strong> untuk masuk ke editor pertanyaan form dinamis.<br>
                    2. Klik tombol <i class="fa-solid fa-upload"></i> <strong>Upload</strong> untuk mengganti template dokumen Word (.docx) dari form tersebut.<br>
                    3. Setiap pertanyaan yang dibuat memiliki <strong>Nama Variabel</strong> (contoh: <code>nama_pemohon</code>) yang harus cocok dengan placeholder <code>${nama_pemohon}</code> di dalam template dokumen Word.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Modal Upload Template -->
<div class="modal fade" id="uploadTemplateModal" tabindex="-1" aria-labelledby="uploadTemplateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="uploadTemplateModalLabel">Upload Template Dokumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="uploadTemplateForm" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body py-4">
              <div class="alert alert-light border mb-4">
                  <strong class="d-block text-dark mb-1" id="modalFormTitle">Form Name</strong>
                  <span class="text-muted" style="font-size: 12px;">File Saat Ini: <code id="modalFormFilename">filename.docx</code></span>
              </div>
              <div class="mb-3">
                  <label for="template_dokumen" class="form-label fw-bold" style="font-size: 13px;">Pilih File Template Baru (.docx)</label>
                  <input class="form-control" type="file" id="template_dokumen" name="template_dokumen" accept=".docx" required style="padding: 10px; border: 2px dashed #cbd5e1; background: #f8fafc;">
                  <div class="form-text" style="font-size: 12px;">Pastikan ekstensi file adalah .docx dan maksimal 5MB.</div>
              </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
              <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                  <i class="fa-solid fa-upload me-1"></i> Upload & Timpa
              </button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function openUploadModal(formType, formTitle, formFilename) {
    // Set text
    document.getElementById('modalFormTitle').innerText = formTitle;
    document.getElementById('modalFormFilename').innerText = formFilename;
    
    // Set form action route
    const form = document.getElementById('uploadTemplateForm');
    form.action = `/form-builder/${formType}/upload-template`;
    
    // Show modal
    var myModal = new bootstrap.Modal(document.getElementById('uploadTemplateModal'));
    myModal.show();
}
</script>
@endsection
