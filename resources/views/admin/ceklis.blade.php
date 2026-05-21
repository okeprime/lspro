@extends('layouts.app')

@section('title', 'Evaluasi Kelengkapan Dokumen - Form 7.2-4')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ url('/aktivitas') }}" class="btn btn-sm btn-light border text-secondary mb-2" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Aktivitas
        </a>
        <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">
            Evaluasi Dokumen Permohonan #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }}
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

    <form action="{{ route('admin.pengajuan.ceklis', $pengajuan->id) }}" method="POST">
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
</table>
                </div>

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
                                    <th colspan="2">Hasil Evaluasi</th>
                                    <th colspan="2">Kebenaran</th>
                                    <th rowspan="2" width="20%">Keterangan</th>
                                </tr>
                                <tr>
                                    <th width="10%">Lengkap</th>
                                    <th width="10%">Tidak</th>
                                    <th width="10%">Benar</th>
                                    <th width="10%">Tidak</th>
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

                                @foreach($list_form as $i => $dokumen)
                                    @php
                                        // Ambil value sebelumnya jika sudah pernah disave
                                        $valEvaluasi = $savedCeklis[$i]['evaluasi'] ?? '';
                                        $valKebenaran = $savedCeklis[$i]['kebenaran'] ?? '';
                                        $valKet = $savedCeklis[$i]['keterangan'] ?? '';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $dokumen }}</td>
                                        
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][evaluasi]" value="lengkap" {{ $valEvaluasi == 'lengkap' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][evaluasi]" value="tidak" {{ $valEvaluasi == 'tidak' ? 'checked' : '' }}>
                                        </td>
                                        
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][kebenaran]" value="benar" {{ $valKebenaran == 'benar' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input border-secondary" type="radio" name="ceklis[{{$i}}][kebenaran]" value="tidak" {{ $valKebenaran == 'tidak' ? 'checked' : '' }}>
                                        </td>
                                        
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="ceklis[{{$i}}][keterangan]" value="{{ $valKet }}" placeholder="Ket...">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
                    @if($pengajuan->file_permohonan)
                        <div class="d-flex flex-column text-center">
                            <i class="bi bi-file-earmark-pdf-fill text-danger mb-2" style="font-size: 2.5rem;"></i>
                            <span class="fw-bold text-truncate mb-3" style="font-size: 14px;">{{ $pengajuan->file_permohonan }}</span>
                            <a href="{{ route('pengajuan.download', $pengajuan->id) }}" class="btn btn-sm btn-outline-danger fw-bold shadow-sm" style="border-radius: 8px;">
                                <i class="bi bi-download"></i> Unduh Berkas
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning border-0 small mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i> Tidak ada berkas fisik dilampirkan.
                        </div>
                    @endif
                </div>

                <div class="card border-0 shadow-sm p-4 text-white sticky-top" style="border-radius: 16px; background-color: #1e293b; top: 20px;">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-clipboard-check text-warning me-2"></i>Kesimpulan Akhir
                    </h5>
                    <p class="small text-white-50">Tentukan kesimpulan berdasarkan tabel evaluasi di samping.</p>
                    <hr class="border-secondary mt-0 mb-4">

                    <div class="mb-4">
                        <label class="form-label fw-bold small d-block mb-2">Pilih Kesimpulan:</label>
                        
                        <div class="form-check bg-dark bg-opacity-25 p-3 mb-2" style="border-radius: 8px; border: 1px solid #334155;">
                            <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusLengkap" value="lengkap" required {{ $pengajuan->status == 'lengkap' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" style="cursor: pointer;" for="statusLengkap">
                                <i class="bi bi-check-circle-fill me-1"></i> LENGKAP & MEMENUHI
                            </label>
                        </div>

                        <div class="form-check bg-dark bg-opacity-25 p-3" style="border-radius: 8px; border: 1px solid #334155;">
                            <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusPerbaikan" value="perbaikan" required {{ $pengajuan->status == 'perbaikan' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-warning" style="cursor: pointer;" for="statusPerbaikan">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> TIDAK LENGKAP (PERBAIKAN)
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-bold small text-white-50">Catatan Verifikator (Opsional):</label>
                        <textarea class="form-control bg-dark text-white border-secondary small shadow-none" id="catatan" name="catatan" rows="3" placeholder="Tuliskan alasan/revisi di sini..." style="border-radius: 8px;">{{ $pengajuan->catatan }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-white-50">Nama Verifikator:</label>
                        <input type="text" name="nama_tu" class="form-control bg-dark text-white border-secondary small shadow-none" style="border-radius: 8px;" value="{{ $pengajuan->nama_tu ?? Auth::user()->name }}">
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2.5 shadow" style="border-radius: 8px; font-size: 14px; color: #1e293b;">
                        <i class="bi bi-floppy-fill me-1"></i> Simpan Hasil Evaluasi
                    </button>
                    
                    <div class="mt-3 text-center text-white-50" style="font-size: 11px;">
                        Sistem Informasi Sertifikasi Produk
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection