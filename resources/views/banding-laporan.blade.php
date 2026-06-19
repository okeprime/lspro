@extends('layouts.app')

@section('title', 'Banding & Keluhan')

@section('extra-css')
<style>
    .banding-page-hero {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 60%, #5b21b6 100%);
        border-radius: 20px;
        padding: 40px 36px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }
    .banding-page-hero::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .banding-page-hero h1 {
        font-size: clamp(18px, 3vw, 24px);
        font-weight: 800;
        margin-bottom: 8px;
    }
    .banding-page-hero p {
        font-size: 14px;
        opacity: 0.85;
        margin: 0;
    }

    .form-section {
        background: white;
        border-radius: 20px;
        padding: 48px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        max-width: 100%;
        margin: 0 auto;
    }
    .form-section h5 {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border-radius: 10px;
        font-size: 14px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        transition: border-color 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.08);
    }
    .btn-submit-banding {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 700;
        width: 100%;
        transition: all 0.2s;
        margin-top: 8px;
    }
    .btn-submit-banding:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
        color: white;
    }
    .jenis-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .jenis-card:hover { border-color: #7c3aed; background: #faf5ff; }
    .jenis-card.selected { border-color: #7c3aed; background: #f5f3ff; }
    .jenis-card input[type="radio"] { accent-color: #7c3aed; width: 16px; height: 16px; }
    .jenis-card .jenis-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }
    .jenis-card .jenis-title { font-size: 13px; font-weight: 700; color: #0f172a; }
    .jenis-card .jenis-desc { font-size: 11px; color: #64748b; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px;">

    {{-- Hero --}}
    <div class="banding-page-hero">
        <div style="position: relative; z-index: 1;">
            <h1><i class="fa-solid fa-gavel me-2"></i> Banding, Keluhan & Laporan</h1>
            <p>Sampaikan keberatan atas keputusan sertifikasi atau keluhan mengenai pelayanan LSPro BRMP SDLP. Setiap pengaduan akan ditangani secara profesional.</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3" style="border-radius: 12px; background: #dcfce7; color: #15803d; font-size: 14px;">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="alert border-0 shadow-sm mb-4" style="border-radius: 12px; background: #fee2e2; color: #991b1b; font-size: 14px;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <strong>Ada kesalahan input:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="form-section" id="banding-form-section">
        <h5><i class="fa-solid fa-paper-plane me-2" style="color: #7c3aed;"></i> Form Pengaduan</h5>

        <form action="{{ route('banding.store') }}" method="POST" enctype="multipart/form-data" id="form-banding-submit">
            @csrf

            {{-- Pilih Jenis --}}
            <div class="mb-4">
                <label class="form-label">Jenis Pengaduan <span class="text-danger">*</span></label>
                <div class="d-flex flex-column gap-2">
                    <label class="jenis-card {{ old('jenis') === 'banding' ? 'selected' : '' }}" id="label-jenis-banding">
                        <input type="radio" name="jenis" value="banding" required {{ old('jenis') === 'banding' ? 'checked' : '' }}>
                        <div class="jenis-icon" style="background: #fee2e2;"><i class="fa-solid fa-gavel" style="color: #dc2626;"></i></div>
                        <div>
                            <div class="jenis-title">Banding</div>
                            <div class="jenis-desc">Keberatan atas keputusan atau hasil penilaian sertifikasi</div>
                        </div>
                    </label>
                    <label class="jenis-card {{ old('jenis') === 'keluhan' ? 'selected' : '' }}" id="label-jenis-keluhan">
                        <input type="radio" name="jenis" value="keluhan" {{ old('jenis') === 'keluhan' ? 'checked' : '' }}>
                        <div class="jenis-icon" style="background: #fef3c7;"><i class="fa-solid fa-comment-dots" style="color: #d97706;"></i></div>
                        <div>
                            <div class="jenis-title">Keluhan</div>
                            <div class="jenis-desc">Mengenai pelayanan, proses, atau sikap petugas LSPro</div>
                        </div>
                    </label>
                    <label class="jenis-card {{ old('jenis') === 'laporan' ? 'selected' : '' }}" id="label-jenis-laporan">
                        <input type="radio" name="jenis" value="laporan" {{ old('jenis') === 'laporan' ? 'checked' : '' }}>
                        <div class="jenis-icon" style="background: #f3f4f6;"><i class="fa-solid fa-flag" style="color: #6b7280;"></i></div>
                        <div>
                            <div class="jenis-title">Laporan Lainnya</div>
                            <div class="jenis-desc">Pelaporan isu lain yang perlu perhatian pihak LSPro</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Terkait Pengajuan --}}
            <div class="mb-4">
                <label class="form-label">Terkait Pengajuan <span class="text-muted fw-normal">(Opsional)</span></label>
                <select name="pengajuan_id" class="form-select" id="select-pengajuan-banding">
                    <option value="">-- Tidak Ada / Bersifat Umum --</option>
                    @foreach($pengajuans as $p)
                        @php
                            $pdf = is_array($p->data_form) ? $p->data_form : (json_decode($p->data_form, true) ?? []);
                        @endphp
                        <option value="{{ $p->id }}" {{ old('pengajuan_id') == $p->id ? 'selected' : '' }}>
                            #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }} – {{ $pdf['merek'] ?? 'Tanpa Merek' }} ({{ ucfirst($p->jenis_pengajuan) }})
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Pilih nomor pengajuan yang terkait dengan pengaduan ini.</div>
            </div>

            {{-- Subjek --}}
            <div class="mb-4">
                <label class="form-label">Subjek Pengaduan <span class="text-danger">*</span></label>
                <input type="text" name="subjek" class="form-control" placeholder="Contoh: Keterlambatan Verifikasi Dokumen" required value="{{ old('subjek') }}">
            </div>

            {{-- Pesan --}}
            <div class="mb-4">
                <label class="form-label">Penjelasan / Pesan <span class="text-danger">*</span></label>
                <textarea name="pesan" rows="10" class="form-control"
                          id="textarea-pesan-banding"
                          placeholder="Uraikan secara rinci banding, keluhan, atau laporan Anda. Sertakan tanggal kejadian, nomor dokumen, dan detail relevan lainnya."
                          required style="resize: vertical; min-height: 200px;">{{ old('pesan') }}</textarea>
            </div>

            {{-- Lampiran --}}
            <div class="mb-4">
                <label class="form-label">Dokumen Lampiran <span class="text-muted fw-normal">(Opsional)</span></label>
                <input type="file" name="file_lampiran" class="form-control" id="input-lampiran-banding"
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip">
                <div class="form-text">Format: PDF, JPG, PNG, DOC, DOCX, ZIP. Maksimal 10MB.</div>
            </div>

            <button type="submit" class="btn btn-submit-banding" id="btn-kirim-banding">
                <i class="fa-solid fa-paper-plane me-2"></i> Kirim Pengaduan
            </button>
        </form>
    </div>

    {{-- Info Box --}}
    <div class="mt-4 p-3 d-flex gap-3 align-items-start" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
        <i class="fa-solid fa-circle-info mt-1" style="color: #0369a1; flex-shrink: 0;"></i>
        <div style="font-size: 13px; color: #475569;">
            Riwayat banding dan keluhan yang sudah Anda kirim dapat dipantau di halaman
            <a href="{{ route('aktivitas.banding') }}" class="fw-semibold text-decoration-none" style="color: #0f766e;">Aktivitas → Banding / Keluhan</a>.
        </div>
    </div>

</div>

<script>
    // Highlight jenis card saat dipilih
    document.querySelectorAll('.jenis-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.jenis-card').forEach(c => c.classList.remove('selected'));
            this.closest('.jenis-card').classList.add('selected');
        });
    });
</script>
@endsection
