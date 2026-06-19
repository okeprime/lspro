@extends('layouts.app')
@section('title', $title . ' - LSPro')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="mb-4">
        <h2 style="color: #1e293b; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 8px;">{{ $title }}</h2>
        <p class="text-muted" style="font-size: 15px;">{{ $desc }}</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-5 text-center">
            <div style="font-size: 60px; color: #cbd5e1; margin-bottom: 20px;">
                <i class="fa-solid fa-person-digging"></i>
            </div>
            <h4 class="fw-bold" style="color: #475569; margin-bottom: 10px;">Fitur Sedang Dalam Pengembangan</h4>
            <p class="text-muted" style="max-width: 500px; margin: 0 auto; line-height: 1.6;">
                Halaman <strong>{{ $title }}</strong> saat ini masih dalam tahap simulasi (dummy data) dan pengembangan antarmuka. 
                Fungsionalitas penuh akan segera diintegrasikan pada pembaruan sistem berikutnya.
            </p>
            
            <div class="mt-5 text-start d-inline-block" style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px dashed #cbd5e1; max-width: 600px; width: 100%;">
                <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-database me-2"></i> Simulasi Data (Dummy)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <thead class="text-muted" style="font-size: 12px; border-bottom: 1px solid #e2e8f0;">
                            <tr>
                                <th>#ID</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @if(isset($mockData) && is_array($mockData))
                                @foreach($mockData as $row)
                                <tr>
                                    <td class="fw-bold">{{ $row['id'] }}</td>
                                    <td>{!! $row['status'] !!}</td>
                                    <td>{{ $row['desc'] }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="fw-bold">001</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    <td>Data simulasi baris pertama</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">002</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td>Data simulasi baris kedua</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">003</td>
                                    <td><span class="badge bg-primary">Proses</span></td>
                                    <td>Data simulasi baris ketiga</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold mt-3">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
