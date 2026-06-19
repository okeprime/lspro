@extends('layouts.app')

@section('title', 'Jadwal Survailen')

@section('extra-css')
<style>
    .survailen-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        overflow: hidden;
    }
    .survailen-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .survailen-body {
        padding: 24px;
    }
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 9999px;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Pemantauan &amp; Survailen</h2>
            <p class="text-muted mb-0">Jadwal pengawasan berkala/survailen tahunan yang dijadwalkan oleh LSPro.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #f0fdfa; border-left: 4px solid #0f766e; color: #115e59;">
        <h6 class="fw-bold mb-1"><i class="fa-solid fa-circle-info me-2"></i> Prosedur Survailen Tahunan</h6>
        <p class="mb-0" style="font-size: 13.5px; opacity: 0.9;">
            Untuk menjaga keabsahan Sertifikat SPPT SNI, LSPro melakukan pengawasan berkala minimal satu kali dalam setahun. Silakan periksa jadwal aktif Anda di bawah, lengkapi persyaratan, dan unggah dokumen bukti implementasi sistem manajemen mutu perusahaan Anda sebelum batas waktu yang ditentukan.
        </p>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($schedules as $schedule)
                @php
                    $df = is_array($schedule->pengajuan->data_form) ? $schedule->pengajuan->data_form : (json_decode($schedule->pengajuan->data_form, true) ?? []);
                    $bulanName = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                @endphp
                <div class="survailen-card">
                    <div class="survailen-header">
                        <div>
                            <span class="badge bg-teal-subtle text-teal px-3 py-1.5 rounded-pill fw-bold text-uppercase mb-2" style="background-color: #f0fdf4; color: #0f766e;">Tahun Survailen: {{ $schedule->survailen_year }}</span>
                            <h4 class="fw-bold mb-1 text-dark" style="font-size: 18px;">{{ $df['merek'] ?? 'Merek Produk' }} - {{ $df['nama_produk'] ?? 'Pupuk' }}</h4>
                            <p class="text-muted mb-0" style="font-size: 13px;">Registrasi Asal: {{ $schedule->pengajuan->nomor_registrasi ?: 'N/A' }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($schedule->status === 'scheduled')
                                <span class="status-badge bg-secondary text-white">Terjadwal</span>
                            @elseif($schedule->status === 'in_progress')
                                <span class="status-badge bg-warning text-dark">Proses Evaluasi</span>
                            @elseif($schedule->status === 'completed')
                                <span class="status-badge bg-success text-white">Selesai</span>
                            @endif
                        </div>
                    </div>
                    <div class="survailen-body">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-calendar-days text-teal me-1"></i> Rincian Jadwal &amp; Persyaratan</h6>
                                <table class="table table-sm table-borderless align-middle" style="font-size: 14px;">
                                    <tr>
                                        <td class="text-muted" style="width: 200px;">Bulan Pengingat</td>
                                        <td>: <strong>{{ $bulanName[$schedule->reminder_month - 1] ?? $schedule->reminder_month }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Batas Pengunggahan</td>
                                        <td>: <strong class="text-danger">{{ $schedule->deadline ? \Carbon\Carbon::parse($schedule->deadline)->translatedFormat('d F Y') : 'Menunggu Admin' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Catatan Petugas</td>
                                        <td>: <span class="text-secondary">{{ $schedule->catatan ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td>: 
                                            @if($schedule->status === 'scheduled')
                                                <span class="badge bg-secondary text-white">Terjadwal</span>
                                            @elseif($schedule->status === 'in_progress')
                                                <span class="badge bg-warning text-dark">Proses Evaluasi</span>
                                            @elseif($schedule->status === 'completed')
                                                <span class="badge bg-success text-white">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm text-center py-5 text-muted" style="border-radius: 12px;">
                    <i class="fa-solid fa-calendar-xmark d-block fs-1 mb-3"></i>
                    Belum ada jadwal pemantauan / survailen aktif untuk perusahaan Anda.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
