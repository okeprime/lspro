@extends('layouts.app')

@section('title', 'Evaluasi Kelengkapan Dokumen - Form 7.2-4')

@section('content')
@php
    $statusNormalized = \App\Support\LsproType5Workflow::normalize($pengajuan->status);
    $isTU = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'layanan') || strtolower(auth()->user()->role) === 'admin';
    $isAudit = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'audit') || strtolower(auth()->user()->role) === 'admin';
    $show724 = in_array($statusNormalized, ['diajukan', 'perbaikan', 'evaluasi_724_tu', 'evaluasi_724_audit', 'menunggu_ttd', 'billing', 'menunggu_persetujuan_jadwal', 'billing_2', 'proses_audit', 'tindakan_perbaikan', 'billing_3', 'proses_lab', 'menunggu_lhp', 'evaluasi', 'billing_4', 'proses_evaluasi', 'keputusan', 'selesai', 'audit_kecukupan']);
    $isAuditPhase = in_array($statusNormalized, ['evaluasi_724_audit', 'menunggu_persetujuan_jadwal', 'billing_2', 'proses_audit', 'tindakan_perbaikan', 'billing_3', 'proses_lab', 'menunggu_lhp', 'evaluasi', 'billing_4', 'proses_evaluasi', 'keputusan', 'selesai', 'audit_kecukupan']);
    $disableEvaluasi = !(in_array($statusNormalized, ['diajukan', 'evaluasi_724_tu', 'audit_kecukupan']) && $isTU);
    $disableKebenaran = !(in_array($statusNormalized, ['evaluasi_724_audit', 'menunggu_persetujuan_jadwal', 'billing_2', 'proses_audit', 'audit_kecukupan']) && $isAudit);
@endphp

<div class="container py-4">
    <div class="mb-4">
        <a href="{{ url()->previous() == request()->url() ? route('admin.dashboard') : url()->previous() }}" class="btn btn-sm btn-light border text-secondary mb-2" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px; display: flex; align-items: center; gap: 10px;">
            Evaluasi Dokumen Permohonan #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }}
            <button type="button" class="btn btn-sm btn-info text-white rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#infoLsproModal" style="font-size: 13px; font-weight: 600;">
                <i class="bi bi-info-circle-fill me-1"></i> Info Ceklis
            </button>
        </h2>
        <p class="text-muted small">Kelengkapan Dan Kebenaran Dokumen Permohonan Sertifikasi Form 7.2-4 LS Pro BBPM SDLP.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $savedCeklis = json_decode($pengajuan->ceklis_dokumen, true) ?? [];
    @endphp

        @if($statusNormalized === 'diajukan')
    <form action="{{ route('admin.pengajuan.terima_awal', $pengajuan->id) }}" method="POST">
    @else
    <form action="{{ route('admin.pengajuan.ceklis', $pengajuan->id) }}" method="POST">
    @endif
        @csrf
        <div class="row g-4">
            
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 16px; background: white;">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-person-lines-fill text-success me-2"></i>Data Pemohon
                    </h5>
                    
                    <table class="table table-bordered align-middle mb-0" style="font-size: 14px;">
    <tr>
        <td class="table-light" width="30%">1. Nama Pemohon</td>
        <td class="fw-bold">
            {{ $formData['nama_klien'] ?? $formData['nama_pemohon'] ?? $pengajuan->user->name ?? 'Klien' }}
        </td>
    </tr>
    <tr>
        <td class="table-light">2. Nomor Permohonan</td>
        <td>#{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }}</td>
    </tr>
    <tr>
        <td class="table-light">3. Alamat</td>
        <td>
            {{ $formData['alamat'] ?? $formData['alamat_perusahaan'] ?? $pengajuan->user->alamat ?? '-' }}
        </td>
    </tr>
    <tr>
        <td class="table-light">4. Jenis Pupuk</td>
        <td>
            {{ $formData['nama_produk'] ?? $formData['jenis_pupuk'] ?? '-' }}
        </td>
    </tr>
    <tr>
        <td class="table-light">5. Merek</td>
        <td>
            {{ $formData['merek'] ?? $formData['merek_dagang'] ?? '-' }}
        </td>
    </tr>
    <tr>
        <td class="table-light">6. SNI</td>
        <td>
            <span class="badge bg-success-subtle text-success">
                {{ $formData['nomor_sni'] ?? $formData['no_sni'] ?? $formData['judul_sni'] ?? '-' }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="table-light">7. Waktu Tempuh</td>
        <td>
            {{ $formData['waktu_pabrik'] ?? '-' }} Menit
        </td>
    </tr>
    <tr>
        <td class="table-light">8. Status Pemaklon</td>
        <td>
            {{ $formData['is_pemaklon'] ?? '-' }}
            @if(isset($formData['is_pemaklon']) && $formData['is_pemaklon'] === 'Ya')
                <br><small class="text-muted">Nama: {{ $formData['nama_pemaklon'] ?? '-' }}</small>
                <br><small class="text-muted">Alamat: {{ $formData['alamat_pemaklon'] ?? '-' }}</small>
            @endif
        </td>
    </tr>
    <tr>
        <td class="table-light">9. File Pendukung Tambahan</td>
        <td>
            @if(!empty($formData['kop_surat']))
                <a href="{{ url('/storage/' . $formData['kop_surat']) }}" target="_blank" class="badge bg-primary text-decoration-none me-1"><i class="fa-solid fa-file me-1"></i> Kop Surat</a>
            @endif
            @if(!empty($formData['sketsa_logo']))
                <a href="{{ url('/storage/' . $formData['sketsa_logo']) }}" target="_blank" class="badge bg-info text-decoration-none me-1"><i class="fa-solid fa-image me-1"></i> Sketsa Logo</a>
            @endif
            @if(!empty($formData['foto_depan']))
                <a href="{{ url('/storage/' . $formData['foto_depan']) }}" target="_blank" class="badge bg-secondary text-decoration-none me-1"><i class="fa-solid fa-camera me-1"></i> Foto Depan</a>
            @endif
            @if(!empty($formData['foto_belakang']))
                <a href="{{ url('/storage/' . $formData['foto_belakang']) }}" target="_blank" class="badge bg-secondary text-decoration-none me-1"><i class="fa-solid fa-camera me-1"></i> Foto Belakang</a>
            @endif
            @if(!empty($formData['foto_kanan']))
                <a href="{{ url('/storage/' . $formData['foto_kanan']) }}" target="_blank" class="badge bg-secondary text-decoration-none me-1"><i class="fa-solid fa-camera me-1"></i> Foto Kanan</a>
            @endif
            @if(!empty($formData['foto_kiri']))
                <a href="{{ url('/storage/' . $formData['foto_kiri']) }}" target="_blank" class="badge bg-secondary text-decoration-none me-1"><i class="fa-solid fa-camera me-1"></i> Foto Kiri</a>
            @endif
        </td>
    </tr>
</table>
                </div>

                @if($show724)
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 16px; background: white;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-ui-checks-grid text-primary me-2"></i>Formulir 7.2-4 / LS Pro
                        </h5>
                    </div>

                    <div class="table-responsive border rounded">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th rowspan="2" width="5%">No</th>
                                    <th rowspan="2" width="35%">Kelengkapan</th>
                                    @if($isAuditPhase)
                                    <th rowspan="2" width="10%">Evaluasi Administrasi</th>
                                    <th colspan="2">Kebenaran (Audit)</th>
                                    @else
                                    <th colspan="2">Hasil Evaluasi (Administrasi)</th>
                                    @endif
                                    <th rowspan="2" width="20%">Keterangan</th>
                                </tr>
                                <tr>
                                    @if($isAuditPhase)
                                    <th width="10%">Benar</th>
                                    <th width="10%">Tidak</th>
                                    @else
                                    <th width="10%">Lengkap</th>
                                    <th width="10%">Tidak</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $list_form = [
                                        "Akte perusahaan",
                                        "Surat Izin Usaha Industri (SIUI)/Tanda Daftar Industri (TDI)",
                                        "Surat Izin Usaha Perdagangan (SIUP)/ Tanda Daftar Usaha Perdagangan (TDUP)",
                                        "Merek dagang/tanda daftar merek",
                                        "Surat Pelimpahan Merek (Jika merek bukan milik pemohon)",
                                        "Surat Penunjukan sebagai Importir (Jika produk impor)",
                                        "API Umum (Jika ada)",
                                        "Struktur organisasi perusahaan",
                                        "Alur Proses Produksi terhadap tahap-tahap pengendalian mutu",
                                        "Daftar Alat dan Mesin Produksi",
                                        "Daftar Peralatan Uji yang telah terkalibrasi",
                                        "Surat Pernyataan Diri dalam Penerapan Sistem Manajemen Mutu yang Sesuai",
                                        "Dokumen Sistem Mutu (Panduan Mutu dan Dokumen Prosedur)",
                                        "Daftar seluruh Prosedur, Instruksi Kerja, dan Formulir untuk sistem manajemen mutu perusahaan",
                                        "Perjanjian sertifikasi",
                                        "Ilustrasi tanda SNI pada Kemasan"
                                    ];
                                @endphp

                                @php
                                    $lampiranKeys = [
                                        'akte_perusahaan', 'izin_usaha_industri', 'siup_tdup', 'sertifikat_merek',
                                        'pelimpahan_merek', 'bukti_importir', 'api_umum', 'struktur_organisasi',
                                        'alur_produksi_mutu', 'daftar_alat_mesin', 'daftar_alat_uji_kalibrasi',
                                        'surat_pernyataan_diri', 'pedoman_mutu', 'daftar_prosedur_ik',
                                        'perjanjian_sertifikasi', 'ilustrasi_tanda_sni'
                                    ];
                                @endphp

                                @foreach($list_form as $i => $dokumen)
                                    @php
                                        // Ambil value sebelumnya jika sudah pernah disave
                                        $valEvaluasi = $savedCeklis[$i]['evaluasi'] ?? '';
                                        $valKebenaran = $savedCeklis[$i]['kebenaran'] ?? '';
                                        $valKet = $savedCeklis[$i]['keterangan'] ?? '';
                                        $key = $lampiranKeys[$i] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>
                                            {{ $dokumen }}
                                            @if($key && !empty($formData[$key]))
                                                <div class="mt-1">
                                                    <a href="#" onclick="previewDoc('{{ url('/unduh/permohonan/lampiran/' . $formData[$key]) }}', '{{ addslashes($dokumen) }}'); return false;" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px; border-radius: 6px;">
                                                        <i class="bi bi-eye me-1"></i> Buka Dokumen
                                                    </a>
                                                </div>
                                            @else
                                                <div class="mt-1 text-danger" style="font-size: 11px;">
                                                    <i class="bi bi-x-circle me-1"></i> Belum diunggah
                                                </div>
                                            @endif
                                        </td>
                                        
                                        @if($isAuditPhase)
                                        <td class="text-center align-middle" style="background-color: #f8fafc;">
                                            @if($valEvaluasi == 'lengkap')
                                                <span class="badge bg-success" style="font-size:10px;">Lengkap</span>
                                            @elseif($valEvaluasi == 'tidak')
                                                <span class="badge bg-danger" style="font-size:10px;">Tidak</span>
                                            @else
                                                <span class="text-muted" style="font-size:10px;">-</span>
                                            @endif
                                            <input type="hidden" name="ceklis[{{$i}}][evaluasi]" value="{{ $valEvaluasi }}">
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][kebenaran]" value="benar" {{ $valKebenaran == 'benar' ? 'checked' : '' }} {{ $disableKebenaran ? 'disabled' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][kebenaran]" value="tidak" {{ $valKebenaran == 'tidak' ? 'checked' : '' }} {{ $disableKebenaran ? 'disabled' : '' }}>
                                        </td>
                                        @else
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][evaluasi]" value="lengkap" {{ $valEvaluasi == 'lengkap' ? 'checked' : '' }} {{ $disableEvaluasi ? 'disabled' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][evaluasi]" value="tidak" {{ $valEvaluasi == 'tidak' ? 'checked' : '' }} {{ $disableEvaluasi ? 'disabled' : '' }}>
                                        </td>
                                        @endif
                                        
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="ceklis[{{$i}}][keterangan]" value="{{ $valKet }}" placeholder="Ket..." {{ ($disableEvaluasi && $disableKebenaran) ? 'disabled' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="alert alert-info mt-3" style="border-radius: 12px; font-size: 14px;">
                    <i class="bi bi-info-circle-fill me-2"></i> Formulir 7.2-4 / Daftar Ceklis belum tersedia karena Klien belum mengunggah dokumen kelengkapan.
                </div>
                @endif
            </div>

            <div class="col-lg-4">

                @if(in_array($pengajuan->status, ['lengkap', 'perbaikan']))
                    <div class="card border-0 shadow-sm p-4 mb-4 text-center" style="border-radius: 16px; background-color: #e0f2fe; border: 1px solid #bae6fd !important;">
                        <i class="bi bi-file-earmark-word-fill text-primary mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold text-dark mb-1">Hasil Ceklis Tersedia</h6>
                        <p class="small text-muted mb-3">Dokumen Form 7.2-4 sudah dievaluasi dan siap diunduh.</p>
                        <a href="{{ route('pengajuan.download724', $pengajuan->id) }}" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-download me-1"></i> Unduh Form 7.2-4
                        </a>
                    </div>
                @endif
                
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 16px; background-color: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-paperclip text-danger me-2"></i>Lampiran Dokumen Klien
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        @if($pengajuan->file_permohonan)
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded" style="background: #fff; font-size: 13px;">
                                <div class="text-truncate me-2">
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                    <strong>Form 7.2-1 Pengajuan</strong>
                                </div>
                                <button type="button" onclick="previewDoc('{{ url('/unduh/permohonan/' . $pengajuan->file_permohonan) }}', 'Form 7.2-1 Pengajuan')" class="btn btn-sm btn-outline-primary" style="font-size: 11px; border-radius: 6px;">
                                    Buka
                                </button>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i> Form Pengajuan tidak ditemukan.
                            </div>
                        @endif
                    </div>
                </div>

                @if($show724)
                <div class="card border-0 shadow-sm p-4 text-white sticky-top" style="border-radius: 16px; background-color: #1e293b; top: 100px;">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-clipboard-check text-warning me-2"></i>Kesimpulan Akhir
                    </h5>
                    <p class="small text-white-50">Tentukan kesimpulan berdasarkan tabel evaluasi di samping.</p>
                    <hr class="border-secondary mt-0 mb-4">

                    @php
                        $defaultCatatan = $isAuditPhase ? ($formData['catatan_audit'] ?? '') : $pengajuan->catatan;
                        $defaultNama = $isAuditPhase ? ($formData['nama_audit'] ?? Auth::user()->name) : ($pengajuan->nama_tu ?? Auth::user()->name);
                        $canSubmitCeklis = in_array($statusNormalized, ['diajukan', 'evaluasi_724_tu', 'evaluasi_724_audit', 'audit_kecukupan', 'proses_audit']);
                    @endphp

                    @if($isAuditPhase)
                    <!-- Tampilkan Review Administrasi Sebelumnya untuk Audit -->
                    <div class="mb-4 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #cbd5e1;">
                        <h6 class="fw-bold mb-2" style="font-size: 12px; color: #334155;"><i class="bi bi-info-circle-fill text-primary me-1"></i> Review Administrasi Sebelumnya:</h6>
                        <div style="font-size: 13px; color: #475569;">
                            <div class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Lengkap & Memenuhi</span></div>
                            <div class="mb-1"><strong>Catatan:</strong> {{ $pengajuan->catatan ?: '-' }}</div>
                            <div><strong>Oleh:</strong> {{ $pengajuan->nama_tu ?: 'Administrasi' }}</div>
                        </div>
                    </div>
                    @endif

                    @if($canSubmitCeklis)
                        <div class="mb-4">
                            <label class="form-label fw-bold small d-block mb-2">Pilih Kesimpulan {{ $isAuditPhase ? '(Tim Audit)' : '(Administrasi)' }}:</label>
                            <div class="d-flex flex-column gap-3 mb-4">
                            @if($pengajuan->status === 'proses_audit')
                                <div class="form-check d-flex align-items-center bg-dark bg-opacity-25 p-3 m-0" style="border-radius: 8px; border: 1px solid #334155; cursor: pointer;" onclick="document.getElementById('statusPPC').click();">
                                    <input class="form-check-input m-0 me-3" type="radio" name="kesimpulan" id="statusPPC" value="menunggu_lhp" required style="width: 18px; height: 18px; cursor: pointer;">
                                    <label class="form-check-label fw-bold text-success mb-0" style="cursor: pointer; font-size: 14px;" for="statusPPC">
                                        DISETUJUI (Lanjut Proses PPC)
                                    </label>
                                </div>
                                <div class="form-check d-flex align-items-center bg-dark bg-opacity-25 p-3 m-0" style="border-radius: 8px; border: 1px solid #334155; cursor: pointer;" onclick="document.getElementById('statusTemuan').click();">
                                    <input class="form-check-input m-0 me-3" type="radio" name="kesimpulan" id="statusTemuan" value="tindakan_perbaikan" required style="width: 18px; height: 18px; cursor: pointer;">
                                    <label class="form-check-label fw-bold text-warning mb-0" style="cursor: pointer; font-size: 14px;" for="statusTemuan">
                                        ADA TEMUAN (Tindakan Perbaikan Klien)
                                    </label>
                                </div>
                            @else
                                <div class="form-check d-flex align-items-center bg-dark bg-opacity-25 p-3 m-0" style="border-radius: 8px; border: 1px solid #334155; cursor: pointer;" onclick="document.getElementById('statusLengkap').click();">
                                    <input class="form-check-input m-0 me-3" type="radio" name="kesimpulan" id="statusLengkap" value="lengkap" required {{ $pengajuan->status == 'lengkap' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                                    <label class="form-check-label fw-bold text-success mb-0" style="cursor: pointer; font-size: 14px;" for="statusLengkap">
                                        {{ $isAuditPhase ? 'BENAR (LENGKAP)' : 'LENGKAP (SESUAI)' }}
                                    </label>
                                </div>
                                <div class="form-check d-flex align-items-center bg-dark bg-opacity-25 p-3 m-0" style="border-radius: 8px; border: 1px solid #334155; cursor: pointer;" onclick="document.getElementById('statusPerbaikan').click();">
                                    <input class="form-check-input m-0 me-3" type="radio" name="kesimpulan" id="statusPerbaikan" value="perbaikan" required {{ $pengajuan->status == 'perbaikan' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                                    <label class="form-check-label fw-bold text-warning mb-0" style="cursor: pointer; font-size: 14px;" for="statusPerbaikan">
                                        {{ $isAuditPhase ? 'TIDAK BENAR (PERBAIKAN)' : 'TIDAK LENGKAP (PERBAIKAN)' }}
                                    </label>
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-bold small text-white-50">Catatan Evaluator (Opsional):</label>
                            <textarea class="form-control bg-dark text-white border-secondary small shadow-none" id="catatan" name="catatan" rows="3" placeholder="Tuliskan alasan/revisi di sini..." style="border-radius: 8px;">{{ $defaultCatatan }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-white-50">Nama Evaluator {{ $isAuditPhase ? '(Audit)' : '(Administrasi)' }}:</label>
                            <input type="text" name="nama_tu" class="form-control bg-dark text-white border-secondary small shadow-none" style="border-radius: 8px;" value="{{ $defaultNama }}">
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2.5 shadow" style="border-radius: 8px; font-size: 14px; color: #1e293b;">
                            <i class="bi bi-floppy-fill me-1"></i> Simpan Hasil Evaluasi
                        </button>
                    @else
                        <div class="alert alert-secondary mt-3 mb-0" style="background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #cbd5e1; border-radius: 8px; font-size: 13px;">
                            <i class="bi bi-lock-fill me-2"></i> Pengisian kesimpulan tidak aktif pada tahap <strong>{{ str_replace('_', ' ', strtoupper($pengajuan->status)) }}</strong>.
                        </div>
                    @endif
                    
                    <div class="mt-3 text-center text-white-50" style="font-size: 11px;">
                        Sistem Informasi Sertifikasi Produk
                    </div>
                </div>
                @else
                <div class="alert alert-info mt-3" style="border-radius: 12px; font-size: 14px;">
                    <i class="bi bi-info-circle-fill me-2"></i> Formulir 7.2-4 / Daftar Ceklis belum tersedia karena Klien belum mengunggah dokumen kelengkapan.
                </div>
                @endif
            </div>

        </div>
    </form>
</div>

<!-- Modal Info LSPro -->
<div class="modal fade" id="infoLsproModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="modal-title fw-bold" style="color: #1e293b;"><i class="bi bi-info-circle-fill text-primary me-2"></i>Panduan Pengisian Ceklis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: 14px; color: #475569;">
                <p><strong>Bagi Administrasi (Administrasi):</strong></p>
                <ul class="mb-3">
                    <li>Periksa apakah dokumen Klien dapat diunduh dan terbaca.</li>
                    <li>Centang <strong>Lengkap</strong> jika dokumen sesuai permintaan, atau <strong>Tidak</strong> jika dokumen belum sesuai/kurang.</li>
                    <li>Hasil Anda akan menjadi acuan bagi Tim Audit.</li>
                </ul>
                <p><strong>Bagi Tim Audit:</strong></p>
                <ul class="mb-0">
                    <li>Anda bertugas memeriksa <strong>Kebenaran</strong> teknis dari dokumen yang sudah dinyatakan Lengkap oleh Administrasi.</li>
                    <li>Centang <strong>Benar</strong> jika isi dokumen secara teknis sudah sesuai regulasi SNI.</li>
                    <li>Gunakan kolom Keterangan untuk mencatat temuan atau ketidaksesuaian teknis.</li>
                </ul>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="border-radius: 8px;">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Dokumen -->
<div class="modal fade" id="previewDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; height: 90vh;">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="modal-title fw-bold" id="previewDocTitle" style="color: #1e293b;">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: calc(90vh - 60px);">
                <iframe id="previewDocIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function previewDoc(url, title) {
        document.getElementById('previewDocTitle').innerText = title;
        
        var lowerUrl = url.toLowerCase();
        if (lowerUrl.endsWith('.doc') || lowerUrl.endsWith('.docx') || lowerUrl.endsWith('.xls') || lowerUrl.endsWith('.xlsx')) {
            // Gunakan Google Docs Viewer untuk file Office
            document.getElementById('previewDocIframe').src = 'https://docs.google.com/gview?url=' + encodeURIComponent(url) + '&embedded=true';
        } else {
            // PDF atau Gambar bisa langsung ditampilkan oleh browser di iframe
            document.getElementById('previewDocIframe').src = url;
        }
        
        var modal = new bootstrap.Modal(document.getElementById('previewDocModal'));
        modal.show();
    }
</script>
@endsection
