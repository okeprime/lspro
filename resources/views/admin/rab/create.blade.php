@extends('layouts.app')

@section('title', 'Pembuatan RAB')

@section('extra-css')
<style>
    .rab-table th {
        background-color: #f8fafc;
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        vertical-align: middle;
    }
    .rab-table td {
        vertical-align: middle;
        font-size: 14px;
    }
    .form-control-rab {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 6px 10px;
        font-size: 14px;
    }
    .kategori-header {
        background-color: #f1f5f9 !important;
        font-weight: 700 !important;
        color: #1e293b !important;
    }
    .total-row {
        background-color: #e2e8f0 !important;
        font-weight: 700;
        font-size: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="color: #1e293b; font-weight: 700;">
                <i class="fa-solid fa-file-invoice me-2"></i> Rencana Anggaran Biaya (RAB)
            </h4>
            <p class="text-muted mb-0" style="font-size: 14px;">
                Pembuatan Master Budget (RAB) - Klien: {{ $pengajuan->user->nama_perusahaan ?? 'Klien' }}
            </p>
        </div>
        <a href="{{ route('admin.panel_keuangan') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.rab.store', $pengajuan->id) }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 rab-table" id="rabTable">
                        <thead>
                            <tr>
                                <th width="25%">Kategori / Komponen</th>
                                <th width="8%" class="text-center">Hari</th>
                                <th width="8%" class="text-center">Orang</th>
                                <th width="20%" class="text-end">Tarif Satuan (Rp)</th>
                                <th width="20%" class="text-end">Total (Rp)</th>
                                <th width="5%" class="text-center"><i class="fa-solid fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $categories = [
                                    'Permohonan' => [['komponen' => 'Permohonan', 'tarif' => 500000]],
                                    'Audit Kecukupan' => [['komponen' => 'Audit Kecukupan', 'tarif' => 1000000]],
                                    'Audit Kesesuaian' => [
                                        ['komponen' => 'Auditor Kepala', 'hari' => 2, 'orang' => 1, 'tarif' => 2000000],
                                        ['komponen' => 'Auditor', 'hari' => 2, 'orang' => 1, 'tarif' => 1500000],
                                        ['komponen' => 'PPC', 'hari' => 1, 'orang' => 1, 'tarif' => 1000000],
                                    ],
                                    'Biaya Di Luar Tarif (BDLT)' => [
                                        ['komponen' => 'Transportasi (Tiket / Travel)'],
                                        ['komponen' => 'Akomodasi / Penginapan'],
                                        ['komponen' => 'Uang Harian', 'hari' => 1, 'orang' => 1],
                                    ],
                                    'Jasa Sidang Komisi Teknis' => [['komponen' => 'Jasa sidang Komisi Teknis', 'tarif' => 3500000]]
                                ];

                                // Jika RAB sudah pernah dibuat, gunakan data dari DB
                                $groupedRab = [];
                                if($rabItems->count() > 0) {
                                    foreach($rabItems as $item) {
                                        $groupedRab[$item->kategori][] = [
                                            'komponen' => $item->komponen,
                                            'hari' => $item->hari,
                                            'orang' => $item->orang,
                                            'tarif' => $item->tarif_pnbp_satuan ? (int)$item->tarif_pnbp_satuan : null,
                                        ];
                                    }
                                    $categories = $groupedRab;
                                }
                            @endphp

                            @foreach($categories as $kategori => $items)
                                <tr class="kategori-row" data-kategori="{{ $kategori }}">
                                    <td colspan="6" class="kategori-header d-flex justify-content-between align-items-center">
                                        <span>{{ $kategori }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-add-row" data-kategori="{{ $kategori }}">
                                            <i class="fa-solid fa-plus"></i> Tambah Baris
                                        </button>
                                    </td>
                                </tr>
                                @foreach($items as $idx => $item)
                                    <tr class="item-row" data-kategori="{{ $kategori }}">
                                        <td class="ps-4">
                                            <input type="text" name="rab[{{ $kategori }}][{{ $idx }}][komponen]" class="form-control form-control-rab w-100" value="{{ $item['komponen'] ?? '' }}" placeholder="Nama Komponen" required>
                                        </td>
                                        <td>
                                            <input type="number" name="rab[{{ $kategori }}][{{ $idx }}][hari]" class="form-control form-control-rab text-center calc-input calc-hari" value="{{ $item['hari'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="number" name="rab[{{ $kategori }}][{{ $idx }}][orang]" class="form-control form-control-rab text-center calc-input calc-orang" value="{{ $item['orang'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="rab[{{ $kategori }}][{{ $idx }}][tarif]" class="form-control form-control-rab text-end money calc-input calc-tarif" value="{{ isset($item['tarif']) && is_numeric($item['tarif']) ? number_format((float)$item['tarif'], 0, '', '.') : '' }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-rab text-end bg-light calc-total-pnbp" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            
                            <tr>
                                <td colspan="4" class="text-end total-row">GRAND TOTAL</td>
                                <td class="text-end total-row text-success" id="grandTotalPnbp">Rp 0</td>
                                <td class="total-row"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan RAB & Update Tagihan
            </button>
        </div>
    </form>
</div>
@endsection

@section('extra-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function cleanNumber(str) {
            if(!str) return 0;
            return parseInt(str.replace(/\./g, '')) || 0;
        }

        function calculateTotals() {
            let grandTotalPnbp = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const hari = parseInt(row.querySelector('.calc-hari').value) || 1;
                const orang = parseInt(row.querySelector('.calc-orang').value) || 1;
                const tarif = cleanNumber(row.querySelector('.calc-tarif').value);
                
                // Jika input tarif kosong, total pnbp = 0
                let totalPnbp = 0;
                if(row.querySelector('.calc-tarif').value !== "") {
                    // Logic khusus RAB: jika hari/orang tidak diisi, anggap pengalinya 1
                    const pengaliHari = row.querySelector('.calc-hari').value !== "" ? hari : 1;
                    const pengaliOrang = row.querySelector('.calc-orang').value !== "" ? orang : 1;
                    totalPnbp = tarif * pengaliHari * pengaliOrang;
                }
                
                row.querySelector('.calc-total-pnbp').value = totalPnbp > 0 ? formatNumber(totalPnbp) : '';
                
                grandTotalPnbp += totalPnbp;
            });

            document.getElementById('grandTotalPnbp').innerText = 'Rp ' + formatNumber(grandTotalPnbp);
        }

        // Masking uang dan event listener
        function bindEvents() {
            document.querySelectorAll('.money').forEach(input => {
                // Hapus event listener lama agar tidak dobel saat nambah baris
                const newEl = input.cloneNode(true);
                input.parentNode.replaceChild(newEl, input);
                
                newEl.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, '');
                    this.value = value ? formatNumber(value) : '';
                    calculateTotals();
                });
            });

            document.querySelectorAll('.calc-input').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            document.querySelectorAll('.btn-remove-row').forEach(btn => {
                btn.onclick = function() {
                    this.closest('tr').remove();
                    calculateTotals();
                };
            });
        }

        // Tambah Baris
        document.querySelectorAll('.btn-add-row').forEach(btn => {
            btn.addEventListener('click', function() {
                const kategori = this.dataset.kategori;
                const tbody = document.querySelector('tbody');
                const timestamp = new Date().getTime();
                
                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.dataset.kategori = kategori;
                tr.innerHTML = `
                    <td class="ps-4">
                        <input type="text" name="rab[${kategori}][${timestamp}][komponen]" class="form-control form-control-rab w-100" placeholder="Nama Komponen" required>
                    </td>
                    <td><input type="number" name="rab[${kategori}][${timestamp}][hari]" class="form-control form-control-rab text-center calc-input calc-hari"></td>
                    <td><input type="number" name="rab[${kategori}][${timestamp}][orang]" class="form-control form-control-rab text-center calc-input calc-orang"></td>
                    <td><input type="text" name="rab[${kategori}][${timestamp}][tarif]" class="form-control form-control-rab text-end money calc-input calc-tarif"></td>
                    <td><input type="text" class="form-control form-control-rab text-end bg-light calc-total-pnbp" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa-solid fa-trash"></i></button>
                    </td>
                `;

                // Cari baris terakhir dari kategori ini
                let lastRow = this.closest('tr'); // Default ke baris header kategori
                const allRows = document.querySelectorAll('tr.item-row');
                allRows.forEach(row => {
                    if(row.dataset.kategori === kategori) {
                        lastRow = row;
                    }
                });
                
                lastRow.insertAdjacentElement('afterend', tr);
                
                bindEvents();
            });
        });

        // Initialize
        bindEvents();
        calculateTotals();
    });
</script>
@endsection
