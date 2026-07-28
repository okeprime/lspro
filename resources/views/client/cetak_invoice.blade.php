<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tagihan - {{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .invoice-container {
            background-color: #ffffff;
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .invoice-header {
            border-bottom: 2px solid #0284c7;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-text {
            color: #0284c7;
            font-weight: bold;
            font-size: 24px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #475569;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
        }
        .total-row th, .total-row td {
            background-color: #e0f2fe;
            font-size: 18px;
            font-weight: bold;
            color: #0284c7;
        }
        .payment-info {
            background-color: #f8fafc;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin-top: 30px;
        }
        @media print {
            body { background-color: #ffffff; }
            .invoice-container { box-shadow: none; margin: 0 auto; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="text-end mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Cetak Dokumen</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <div class="logo-text">LSPro BBPM SDLP</div>
                <small class="text-muted">Kementerian Pertanian Republik Indonesia</small>
            </div>
            <div class="text-end">
                <div class="invoice-title">{{ $invoice->status === 'paid' ? 'KUITANSI' : 'INVOICE' }}</div>
                <div><strong>No:</strong> {{ $invoice->invoice_number }}</div>
                <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        @php
            $df = is_array($invoice->pengajuan->data_form) ? $invoice->pengajuan->data_form : (json_decode($invoice->pengajuan->data_form, true) ?? []);
        @endphp

        <!-- Info Klien & Tagihan -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="text-muted mb-1">Ditagihkan Kepada:</h6>
                <div class="fw-bold fs-5">{{ $df['nama_perusahaan'] ?? $invoice->pengajuan->user->nama_perusahaan ?? 'Nama Perusahaan' }}</div>
                <div>{{ $df['nama_pemohon'] ?? $invoice->pengajuan->user->name ?? 'Nama Pemohon' }}</div>
                <div>{{ $df['alamat_kantor'] ?? $invoice->pengajuan->user->alamat ?? '-' }}</div>
            </div>
            <div class="col-sm-6 text-end">
                <h6 class="text-muted mb-1">Detail Sertifikasi:</h6>
                <div><strong>Produk:</strong> {{ $df['nama_produk'] ?? 'Pupuk' }}</div>
                <div><strong>Merek:</strong> {{ $df['merek'] ?? 'Merek Produk' }}</div>
                <div><strong>Jatuh Tempo:</strong> <span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F Y') }}</span></div>
            </div>
        </div>

        <!-- Rincian Biaya -->
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 60%">Deskripsi Tagihan</th>
                    <th class="text-end" style="width: 35%">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kategoriTarget = '';
                    if ($invoice->jenis_tagihan === 'billing_1') $kategoriTarget = 'Permohonan';
                    elseif ($invoice->jenis_tagihan === 'billing_2') $kategoriTarget = 'Audit Kecukupan';
                    elseif ($invoice->jenis_tagihan === 'billing_2_bdlt') $kategoriTarget = 'Biaya Di Luar Tarif (BDLT)';
                    elseif ($invoice->jenis_tagihan === 'billing_3') $kategoriTarget = 'Audit Kesesuaian';
                    elseif ($invoice->jenis_tagihan === 'billing_3_bdlt') $kategoriTarget = 'Biaya Di Luar Tarif (BDLT)';
                    elseif ($invoice->jenis_tagihan === 'billing_4') $kategoriTarget = 'Evaluasi / Sidang Komtek';
                    
                    $rabItems = [];
                    if ($kategoriTarget) {
                        $rabItems = \App\Models\RabItem::where('pengajuan_id', $invoice->pengajuan_id)
                            ->where('kategori', 'like', '%' . $kategoriTarget . '%')
                            ->get();
                    }
                @endphp
                
                @if(count($rabItems) > 0)
                    @foreach($rabItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->komponen }}</strong><br>
                                <small class="text-muted">
                                    {{ number_format($item->tarif_pnbp_satuan, 0, ',', '.') }} 
                                    @if($item->hari) x {{ $item->hari }} Hari @endif
                                    @if($item->orang) x {{ $item->orang }} Orang @endif
                                </small>
                            </td>
                            <td class="text-end">{{ number_format($item->tarif_pnbp_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <strong>Biaya Sertifikasi LSPro</strong><br>
                            <small class="text-muted">{{ $invoice->notes }}</small>
                        </td>
                        <td class="text-end">{{ number_format($invoice->amount_total, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="2" class="text-end">TOTAL TAGIHAN</th>
                    <td class="text-end">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Status Pembayaran -->
        <div class="mb-4">
            @if($invoice->status === 'paid')
                <h3 class="text-success text-center border border-success rounded py-2 text-uppercase" style="border-width: 3px !important; transform: rotate(-5deg); width: 200px; margin: 0 auto; opacity: 0.8;">LUNAS</h3>
            @endif
        </div>

        <!-- Info Pembayaran -->
        @if($invoice->status !== 'paid')
        <div class="payment-info">
            <h6 class="fw-bold mb-2">Instruksi Pembayaran:</h6>
            <p class="mb-1 text-muted" style="font-size: 14px;">Silakan lakukan transfer pembayaran sejumlah nominal di atas ke salah satu rekening berikut:</p>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-3">
                            <div class="fw-bold">Bank Mandiri</div>
                            <div>No. Rek: <strong>133-00-998822-1</strong></div>
                            <div>A.N: BPN BRMP SDLP - LSPro</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-3">
                            <div class="fw-bold">E-Wallet DANA</div>
                            <div>No. Hp: <strong>0895-2270-4092</strong></div>
                            <div>A.N: MUHAMAD RAKHA BUANA</div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 text-muted" style="font-size: 13px;"><em>* Setelah melakukan pembayaran, harap segera mengunggah bukti transfer melalui sistem (Menu Billing) untuk proses verifikasi.</em></p>
        </div>
        @endif

        <div class="text-center mt-5 text-muted" style="font-size: 12px;">
            <p>Dokumen ini dihasilkan secara otomatis oleh sistem aplikasi LSPro BBPM SDLP.<br>Tidak memerlukan tanda tangan basah.</p>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    // Opsional: otomatis buka pop-up print saat halaman dimuat
    window.onload = function() {
        // Hapus komentar di bawah jika ingin otomatis nge-print saat dibuka
        // window.print();
    }
</script>
</body>
</html>
