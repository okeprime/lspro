@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">Aktivitas Evaluasi Pengajuan</h2>
            <p class="text-muted">Pantau status evaluasi dan audit pengajuan sertifikasi, survailen, dan resertifikasi Anda.</p>
        </div>
    </div>

    @if($pengajuans->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID Pengajuan</th>
                        <th>Tipe Pengajuan</th>
                        <th>Jenis Pengajuan</th>
                        <th>Status Evaluasi</th>
                        <th>Jumlah Temuan</th>
                        <th>Tanggal Mulai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuans as $p)
                        <tr>
                            <td><strong>#{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>
                                @if($p->jenis_pengajuan == 'Sertifikasi')
                                    <span class="badge bg-primary">Sertifikasi</span>
                                @elseif($p->jenis_pengajuan == 'Survailen')
                                    <span class="badge bg-info">Survailen</span>
                                @elseif($p->jenis_pengajuan == 'Resertifikasi')
                                    <span class="badge bg-warning">Resertifikasi</span>
                                @endif
                            </td>
                            <td>{{ $p->status ?? '-' }}</td>
                            <td>
                                @if($p->status == 'proses_audit')
                                    <span class="badge bg-warning text-dark">Proses Audit</span>
                                @elseif($p->status == 'proses_evaluasi')
                                    <span class="badge bg-info">Dalam Evaluasi</span>
                                @elseif($p->status == 'keputusan')
                                    <span class="badge bg-primary">Menunggu Keputusan</span>
                                @else
                                    <span class="badge bg-secondary">{{ $p->status }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $p->auditFindings->count() }} Temuan</span>
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('pengajuan.download', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Download berkas">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            <i class="fa-solid fa-circle-info"></i> Belum ada pengajuan yang sedang dalam proses evaluasi.
        </div>
    @endif
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(167, 243, 208, 0.05);
    }
</style>
@endsection
