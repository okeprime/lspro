@extends('layouts.app')

@section('title', 'Isi Dokumen Dinamis - ' . $typeMeta['title'])

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ url()->previous() == request()->url() ? route('admin.dashboard') : url()->previous() }}" class="btn btn-sm btn-light border text-secondary mb-2" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">
            <i class="{{ $typeMeta['icon'] }} me-2" style="color: {{ $typeMeta['color'] }}"></i>
            Generate Dokumen: {{ $typeMeta['title'] }}
        </h2>
        <p class="text-muted small">ID Pengajuan: #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }} | Pemohon: {{ $pengajuan->user->name }}</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.pengajuan.simpan_form', ['id' => $pengajuan->id, 'form_type' => $form_type]) }}" method="POST">
                @csrf
                
                @foreach($formFieldsBySection as $sectionName => $fields)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header text-white" style="background: {{ $typeMeta['color'] }}; padding: 12px 20px;">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-folder2-open me-2"></i> {{ $sectionName }}</h6>
                        </div>
                        <div class="card-body p-4">
                            @foreach($fields as $field)
                                <div class="mb-4">
                                    <label for="{{ $field->name }}" class="form-label fw-bold text-dark" style="font-size: 14px;">
                                        {{ $field->label }} @if($field->is_required) <span class="text-danger">*</span> @endif
                                    </label>

                                    @if($field->type === 'textarea')
                                        <textarea class="form-control @error($field->name) is-invalid @enderror" id="{{ $field->name }}" name="{{ $field->name }}" rows="3" {{ $field->is_required ? 'required' : '' }}>{{ old($field->name) }}</textarea>
                                    
                                    @elseif($field->type === 'select' && $field->options)
                                        <select class="form-select @error($field->name) is-invalid @enderror" id="{{ $field->name }}" name="{{ $field->name }}" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">-- Pilih --</option>
                                            @foreach($field->options as $opt)
                                                <option value="{{ trim($opt) }}" {{ old($field->name) == trim($opt) ? 'selected' : '' }}>{{ trim($opt) }}</option>
                                            @endforeach
                                        </select>
                                    
                                    @elseif($field->type === 'radio' && $field->options)
                                        <div class="mt-2">
                                            @foreach($field->options as $opt)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input @error($field->name) is-invalid @enderror" type="radio" name="{{ $field->name }}" id="{{ $field->name }}_{{ Str::slug($opt) }}" value="{{ trim($opt) }}" {{ old($field->name) == trim($opt) ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}>
                                                    <label class="form-check-label" for="{{ $field->name }}_{{ Str::slug($opt) }}">{{ trim($opt) }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                    @else
                                        <input type="{{ $field->type === 'date' ? 'date' : ($field->type === 'number' ? 'number' : ($field->type === 'email' ? 'email' : 'text')) }}" 
                                               class="form-control @error($field->name) is-invalid @enderror" 
                                               id="{{ $field->name }}" 
                                               name="{{ $field->name }}" 
                                               value="{{ old($field->name) }}"
                                               {{ $field->is_required ? 'required' : '' }}>
                                    @endif

                                    @error($field->name)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ url()->previous() }}" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn px-4 text-white" style="background: {{ $typeMeta['color'] }}">
                        <i class="bi bi-file-earmark-word-fill me-1"></i> Generate Dokumen (.docx)
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4 bg-light">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Form</h6>
                    <p class="text-muted small mb-2">Formulir ini menggunakan arsitektur dinamis. Pertanyaan-pertanyaan di sebelah kiri diambil secara langsung dari konfigurasi <strong>Form Builder</strong> Superadmin.</p>
                    <p class="text-muted small mb-0">Setelah di-submit, sistem akan mengisi *placeholder* pada template <code>{{ $typeMeta['filename'] }}</code> dan langsung mengunduh hasilnya ke komputer Anda.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
