@extends('layouts.app')

@section('title', 'Dashboard Kerja')

@section('content')
    <div style="margin-bottom: 30px;">
        <h2 style="color: #1e293b;">Dashboard Kerja Klien</h2>
        <p style="color: #64748b; font-size: 14px;">Ringkasan aktivitas dan status pengajuan sertifikasi Anda.</p>
    </div>
    
    <div class="info-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 30px;">
        <div class="info-card" style="border-top-color: #2563eb; text-align: center; padding: 30px;">
            <h4 style="font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Total Pengajuan</h4>
            <h2 style="font-size: 32px; color: #1e293b;">{{ $stats['total'] }}</h2>
        </div>
        <div class="info-card" style="border-top-color: #f59e0b; text-align: center; padding: 30px;">
            <h4 style="font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Sedang Diproses</h4>
            <h2 style="font-size: 32px; color: #1e293b;">{{ $stats['proses'] }}</h2>
        </div>
        <div class="info-card" style="border-top-color: #10b981; text-align: center; padding: 30px;">
            <h4 style="font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Sertifikat Terbit</h4>
            <h2 style="font-size: 32px; color: #1e293b;">{{ $stats['selesai'] }}</h2>
        </div>
    </div>

    <div class="info-card" style="border-top-color: #064e3b; padding: 0;">
        <div style="padding: 20px; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-size: 16px; color: #1e293b;"><i class="fa-solid fa-clock-rotate-left" style="color: #064e3b; margin-right: 10px;"></i> Aktivitas Terbaru</h3>
        </div>
        <div style="padding: 20px;">
            <table class="status-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tahap / Klausul</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aktivitas_terbaru as $at)
                    <tr>
                        <td>{{ $at->created_at->format('d/m/Y') }}</td>
                        <td><strong>Klausul {{ $at->tahap }}</strong></td>
                        <td><span style="color: #2563eb; font-weight: 700;">{{ $at->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection