<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kesesuaian SNI - {{ $pengajuan->data_form['nama_perusahaan'] ?? 'Perusahaan' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }
        .print-area {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            box-sizing: border-box;
            position: relative;
        }
        
        .outer-border-box {
            border: 2px solid #000;
            width: 100%;
            position: relative;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo-col {
            width: 20%;
            padding: 10px;
            border-right: 2px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo-col img {
            max-width: 80px;
        }
        .header-text-col {
            width: 80%;
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .header-title {
            font-weight: bold;
            font-size: 15px;
            line-height: 1.2;
            border-bottom: 2px solid #000;
            padding: 5px;
        }
        .header-subtitle {
            font-size: 10px;
            padding: 5px;
            line-height: 1.2;
        }
        
        .cert-title-bar {
            text-align: center;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
        }
        
        .main-box {
            padding: 20px 30px;
            min-height: 600px;
            position: relative;
        }
        
        .lspro-header {
            text-align: center;
            font-size: 15px;
            line-height: 1.3;
            margin-bottom: 10px;
        }
        
        .divider {
            border-top: 2px solid #00a4e4;
            margin: 10px -30px 20px -30px;
        }
        
        .cert-name {
            text-align: center;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .cert-name .id { font-weight: bold; text-decoration: underline; font-size: 16px; }
        .cert-name .en { font-size: 14px; }
        .cert-name .no { margin-top: 5px; font-size: 14px; }
        
        .recipient-section {
            text-align: center;
            margin: 30px 0;
            line-height: 1.8;
            font-size: 14px;
        }
        .data-line {
            display: block;
            width: 100%;
            text-align: center;
            font-weight: bold;
            border-bottom: none;
        }
        
        .content-text {
            font-size: 14px;
            text-align: justify;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .date-section {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        
        .signature-section {
            float: right;
            text-align: center;
            font-size: 14px;
            width: 300px;
            margin-top: 0px;
        }
        .signature-space {
            height: 60px;
        }
        .signature-name {
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
        }
        
        .footer-marks {
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        /* Print Styles */
        @media print {
            body { background: transparent; padding: 0; }
            .print-btn-container { display: none; }
            .print-area { margin: 0; box-shadow: none; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        /* Web View Styles */
        @media screen {
            body { background: #525659; padding: 40px 0; }
            .print-area { box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
            .print-btn-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
            }
            .btn-print {
                background: #16a34a;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-family: Arial, sans-serif;
                font-weight: bold;
                cursor: pointer;
                box-shadow: 0 4px 6px rgba(22, 163, 74, 0.3);
            }
            .btn-print:hover { background: #15803d; }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>

    @php
        $certNumber = 'SNI-BBPM-' . date('Y', strtotime($pengajuan->updated_at)) . '-' . str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
        $issueDate = \Carbon\Carbon::parse($pengajuan->updated_at);
        $expiryDate = $issueDate->copy()->addYears(4);
        
        // Data Perusahaan
        $namaPemohon = strtoupper($pengajuan->data_form['nama_perusahaan'] ?? 'NAMA PEMOHON');
        $alamatPemohon = $pengajuan->data_form['alamat_kantor'] ?? 'ALAMAT PEMOHON';
        
        // Ruang Lingkup / Produk
        $jenisProduk = $pengajuan->data_form['jenis_produk'] ?? 'pupuk';
        $ruangLingkup = strtoupper($pengajuan->data_form['nama_produk'] ?? 'RUANG LINGKUP SERTIFIKASI');
    @endphp

    <div class="print-area">
        
        <div class="outer-border-box">
            
            <table class="header-table">
                <tr>
                    <td class="header-logo-col">
                        <img src="{{ asset('assets/kementan.png') }}" alt="Logo Kementan">
                    </td>
                    <td class="header-text-col">
                        <div class="header-title">
                            KEMENTERIAN PERTANIAN<br>
                            BADAN PERAKITAN DAN MODERNISASI PERTANIAN<br>
                            LEMBAGA SERTIFIKASI PRODUK<br>
                            BBPM SDLP
                        </div>
                        <div class="header-subtitle">
                            Sekretariat : Jl. Tentara Pelajar No.12, Ciwaringin, Bogor Tengah, Bogor 16124<br>
                            Telp (0251) 8323012 Faks (021) 78830206<br>
                            Email : lspro.pupes.bbpsisdlp@gmail.com
                        </div>
                    </td>
                </tr>
            </table>
            
            <div class="cert-title-bar">
                SERTIFIKAT KESESUAIAN SNI
            </div>
            
            <div class="main-box">
                <div class="lspro-header">
                    LEMBAGA SERTIFIKASI PRODUK<br>
                    BALAI BESAR PERAKITAN DAN MODERNISASI<br>
                    SUMBER DAYA LAHAN PERTANIAN<br>
                    KEMENTERIAN PERTANIAN<br>
                    Jl. Tentara Pelajar No 12 Ciwaringin, Bogor Tengah, Kota Bogor, Jawa Barat
                </div>
                
                <div class="divider"></div>
                
                <div class="cert-name">
                    <div class="id">SERTIFIKAT KESESUAIAN</div>
                    <div class="en">CONFORMITY CERTIFICATE</div>
                    <div class="no">No. {{ $certNumber }}</div>
                </div>
                
                <div class="recipient-section">
                    <div style="margin-bottom: 10px;">diberikan kepada :</div>
                    <div class="data-line" style="margin-bottom: 15px;">{{ $namaPemohon }}</div>
                    <div class="data-line">{{ $alamatPemohon }}</div>
                </div>
                
                <div class="content-text">
                    Berdasarkan hasil sertifikasi kesesuaian yang mengacu pada SNI 17065 : 2012 maka produk berupa {{ $jenisProduk }} dengan ruang lingkup sertifikasi <strong>{{ $ruangLingkup }}</strong> telah memenuhi persyaratan sehingga dinyatakan berhak mendapatkan sertifikat kesesuaian SNI.
                </div>
                
                <table class="date-section" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <td width="230">diterbitkan di Bogor, pada tanggal</td>
                        <td width="10">:</td>
                        <td><strong>{{ $issueDate->translatedFormat('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td>dan berlaku sampai dengan tanggal</td>
                        <td>:</td>
                        <td><strong>{{ $expiryDate->translatedFormat('d F Y') }}</strong></td>
                    </tr>
                </table>
                
                <div class="content-text">
                    Sertifikat ini berlaku dengan ketentuan bahwa organisasi selalu memenuhi kriteria sebagaimana ditetapkan oleh LS Pro BBPM SDLP. Sertifikat ini berlaku 4 tahun dari sejak diterbitkan.
                </div>
                
                <div class="signature-section">
                    Ketua LS Pro BBPSI SDLP
                    <div class="signature-space"></div>
                    (<span class="signature-name">Anik Dwi Hastuti S.P.,M.M</span>)
                </div>
                
                <div style="clear: both;"></div>
            </div> <!-- End of main-box -->
            
        </div> <!-- End of outer-border-box -->
        
        <div class="footer-marks">
            <div>Terbitan/Revisi : 2/-</div>
            <div>FORM 7.7-2 / LS Pro</div>
        </div>
        
    </div>

</body>
</html>