@extends('layouts.app')

@section('title', 'Edit — ' . $typeMeta['title'])

@section('extra-css')
<style>
    .editor-header {
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .editor-header h2 { font-size: 20px; font-weight: 800; margin: 0 0 4px 0; }
    .editor-header p  { font-size: 13px; opacity: 0.8; margin: 0; }

    .section-tab-nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .section-tab-btn {
        padding: 9px 18px; border-radius: 50px; border: 2px solid #e2e8f0;
        background: white; color: #64748b; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .section-tab-btn.active {
        background: {{ $typeMeta['color'] }}; border-color: {{ $typeMeta['color'] }}; color: white;
        box-shadow: 0 4px 12px {{ $typeMeta['color'] }}44;
    }
    .section-tab-btn:hover:not(.active) { border-color: {{ $typeMeta['color'] }}; color: {{ $typeMeta['color'] }}; }
    .section-panel { display: none; }
    .section-panel.active { display: block; }

    .field-card {
        background: white; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 10px;
        display: flex; align-items: center; gap: 14px; transition: all 0.2s;
    }
    .field-card:hover { border-color: {{ $typeMeta['color'] }}; box-shadow: 0 4px 16px {{ $typeMeta['color'] }}14; }
    .field-order {
        width: 34px; height: 34px; border-radius: 50%; background: #f1f5f9;
        color: #475569; font-weight: 800; font-size: 13px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .field-info { flex: 1; min-width: 0; }
    .field-label { font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 2px; }
    .field-meta { font-size: 12px; color: #64748b; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
    .field-actions { display: flex; gap: 8px; flex-shrink: 0; }
    .badge-type { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #eff6ff; color: #1d4ed8; }
    .badge-req { background: #f0fdf4; color: #15803d; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
    .badge-opt { background: #f8fafc; color: #64748b; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
    .section-empty { text-align: center; padding: 48px 20px; color: #94a3b8; }
    .section-empty i { font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.4; }

    @media (max-width: 768px) {
        .editor-header { padding: 20px; }
        .field-card { flex-direction: column; align-items: flex-start; gap: 10px; }
        .field-actions { width: 100%; justify-content: flex-end; }
    }
</style>
@endsection

@section('content')
@php
    $fieldsBySection = $fields->groupBy('section');
    $sections = $fieldsBySection->keys();
@endphp

<div class="container-fluid py-4">

    {{-- Breadcrumb --}}
    <nav style="margin-bottom: 16px;">
        <a href="{{ route('superadmin.form_builder.index') }}" class="text-decoration-none" style="color: {{ $typeMeta['color'] }}; font-weight: 600; font-size: 14px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Formulir
        </a>
    </nav>

    {{-- Header --}}
    <div class="editor-header" style="background: linear-gradient(135deg, {{ $typeMeta['color'] }}dd 0%, {{ $typeMeta['color'] }} 100%);">
        <div>
            <h2><i class="{{ $typeMeta['icon'] }} me-2" style="opacity:0.8;"></i>{{ $typeMeta['title'] }}</h2>
            <p>{{ $typeMeta['description'] }}</p>
        </div>
        <button class="btn btn-light fw-bold px-4 shadow-sm" style="border-radius: 50px; color: {{ $typeMeta['color'] }};"
                data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa-solid fa-plus me-2"></i> Tambah Pertanyaan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Section Tabs --}}
    @if($sections->isNotEmpty())
        <div class="section-tab-nav">
            @foreach($sections as $section)
                <button class="section-tab-btn {{ $loop->first ? 'active' : '' }}"
                        onclick="switchSection('{{ Str::slug($section, '_') }}')"
                        id="tab-{{ Str::slug($section, '_') }}">
                    <i class="fa-solid fa-folder-open me-1" style="font-size: 11px;"></i>
                    {{ $section }}
                    <span class="ms-1" style="background: rgba(0,0,0,0.08); border-radius: 10px; padding: 1px 7px; font-size: 11px;">
                        {{ $fieldsBySection[$section]->count() }}
                    </span>
                </button>
            @endforeach
        </div>
    @endif

    {{-- Section Panels --}}
    @forelse($sections as $section)
        @php $sectionFields = $fieldsBySection[$section]; @endphp
        <div class="section-panel {{ $loop->first ? 'active' : '' }}" id="panel-{{ Str::slug($section, '_') }}">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $section }}</h5>
                    <small class="text-muted">{{ $sectionFields->count() }} pertanyaan</small>
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                        data-bs-toggle="modal" data-bs-target="#modalCreate"
                        onclick="prefillSection('{{ addslashes($section) }}')"
                        style="border-color: {{ $typeMeta['color'] }}; color: {{ $typeMeta['color'] }};">
                    <i class="fa-solid fa-plus me-1"></i> Tambah ke Bagian Ini
                </button>
            </div>

            @foreach($sectionFields->sortBy('order_index') as $field)
                <div class="field-card">
                    <div class="field-order">{{ $field->order_index }}</div>
                    <div class="field-info">
                        <div class="field-label">
                            {{ $field->label }}
                            @if($field->is_required)<span class="text-danger ms-1">*</span>@endif
                        </div>
                        <div class="field-meta">
                            <span><code class="text-primary" style="font-size:12px;">${{ $field->name }}</code></span>
                            <span class="badge-type">{{ strtoupper($field->type) }}</span>
                            <span class="{{ $field->is_required ? 'badge-req' : 'badge-opt' }}">
                                {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                            </span>
                            @if($field->options)
                                <span style="font-size:11px;color:#94a3b8;">
                                    <i class="fa-solid fa-list me-1"></i>{{ implode(', ', (array)$field->options) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="field-actions">
                        <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;"
                                data-bs-toggle="modal" data-bs-target="#modalEdit{{ $field->id }}">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                        </button>
                        <form action="{{ route('superadmin.form_builder.destroy', $field->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                    onclick="return confirm('Hapus pertanyaan \'{{ $field->label }}\'?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Modal Edit --}}
                <div class="modal fade" id="modalEdit{{ $field->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
                        <form action="{{ route('superadmin.form_builder.update', $field->id) }}" method="POST" class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
                            @csrf @method('PUT')
                            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                                <div>
                                    <h5 class="modal-title fw-bold text-dark mb-0">Edit Pertanyaan</h5>
                                    <small class="text-muted">{{ $field->label }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold" style="font-size:13px;">Bagian (Section)</label>
                                        <input type="text" name="section" class="form-control" value="{{ $field->section }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold" style="font-size:13px;">Label Pertanyaan</label>
                                        <input type="text" name="label" class="form-control" value="{{ $field->label }}" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold" style="font-size:13px;">Nama Variabel</label>
                                        <div class="input-group">
                                            <span class="input-group-text text-muted">$</span>
                                            <input type="text" name="name" class="form-control font-monospace" value="{{ $field->name }}" required>
                                        </div>
                                        <small class="text-muted">Di template .docx tulis <code>${{ $field->name }}</code></small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold" style="font-size:13px;">Urutan</label>
                                        <input type="number" name="order_index" class="form-control" value="{{ $field->order_index }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold" style="font-size:13px;">Tipe Input</label>
                                        <select name="type" class="form-select" required>
                                            @foreach(['text'=>'Teks Pendek','textarea'=>'Teks Panjang','number'=>'Angka','email'=>'Email','date'=>'Tanggal','select'=>'Dropdown'] as $v=>$l)
                                                <option value="{{ $v }}" {{ $field->type==$v?'selected':'' }}>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end pb-1">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_required" id="req{{ $field->id }}" {{ $field->is_required ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="req{{ $field->id }}">Wajib Diisi?</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold" style="font-size:13px;">Pilihan Dropdown</label>
                                        <input type="text" name="options" class="form-control" value="{{ $field->options ? implode(', ', (array)$field->options) : '' }}" placeholder="Pisahkan dengan koma">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <i class="{{ $typeMeta['icon'] }} text-muted" style="font-size:48px;opacity:0.3;"></i>
            <h5 class="fw-bold text-muted mt-3">Belum Ada Pertanyaan</h5>
            <p class="text-muted" style="font-size:13px;">Klik "Tambah Pertanyaan" untuk mulai menambahkan isian ke formulir <strong>{{ $typeMeta['title'] }}</strong>.</p>
        </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
        <form action="{{ route('superadmin.form_builder.store') }}" method="POST" class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            @csrf
            <input type="hidden" name="form_type" value="{{ $type }}">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title fw-bold text-dark">Tambah Pertanyaan — {{ $typeMeta['title'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size:13px;">Bagian (Section) <span class="text-danger">*</span></label>
                        <input type="text" name="section" id="createSectionInput" class="form-control" required placeholder="Contoh: 1. Identitas Pemohon">
                        <small class="text-muted">Tulis nama bagian yang sama persis untuk mengelompokkan.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size:13px;">Label Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" required placeholder="Contoh: Nama Pemohon">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold" style="font-size:13px;">Nama Variabel <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">$</span>
                            <input type="text" name="name" class="form-control font-monospace" required placeholder="nama_pemohon">
                        </div>
                        <small class="text-muted">Huruf kecil, angka, underscore. Digunakan di template .docx</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:13px;">Urutan</label>
                        <input type="number" name="order_index" class="form-control" value="{{ ($fields->max('order_index') ?? 0) + 1 }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:13px;">Tipe Input</label>
                        <select name="type" class="form-select" required>
                            <option value="text">Teks Pendek</option>
                            <option value="textarea">Teks Panjang</option>
                            <option value="number">Angka</option>
                            <option value="email">Email</option>
                            <option value="date">Tanggal</option>
                            <option value="select">Pilihan Dropdown</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-1">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_required" id="reqCreate" checked>
                            <label class="form-check-label fw-bold" for="reqCreate">Wajib Diisi?</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size:13px;">Pilihan Dropdown</label>
                        <input type="text" name="options" class="form-control" placeholder="Contoh: Ya, Tidak (pisahkan koma)">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn px-4 fw-bold text-white" style="background: {{ $typeMeta['color'] }};">
                    <i class="fa-solid fa-plus me-1"></i> Tambah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-js')
<script>
function switchSection(sectionId) {
    document.querySelectorAll('.section-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.section-tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('panel-' + sectionId);
    const tab   = document.getElementById('tab-' + sectionId);
    if (panel) panel.classList.add('active');
    if (tab)   tab.classList.add('active');
}
function prefillSection(sectionName) {
    const input = document.getElementById('createSectionInput');
    if (input) input.value = sectionName;
}
</script>
@endsection
