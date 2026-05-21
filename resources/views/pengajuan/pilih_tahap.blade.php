@extends('layouts.app')

@section('title', 'Pilih Jenis Layanan Pengajuan Sertifikasi')

@section('content')
    <div style="margin-bottom: 30px;">
        <h2 style="color: #1e293b; font-weight: 700;">Pilih Jenis Layanan Sertifikasi SPPT SNI</h2>
        <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Silakan pilih jenis formulir atau tahapan layanan yang ingin Anda proses secara otomatis.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px;">
        
        <div class="info-card" style="border-top: 5px solid #198754; position: relative; overflow: hidden; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position: absolute; top: 10px; right: 15px; color: #f1f5f9; font-size: 45px; font-weight: 800; opacity: 0.15;">F1</div>
            <div>
                <h3 style="color: #198754; margin-bottom: 12px; font-size: 1.25rem; font-weight: 700;">
                    <i class="fa-solid fa-file-circle-plus"></i> Form 1
                </h3>
                <h4 style="font-size: 14px; color: #334155; font-weight: 600; margin-bottom: 8px;">Pengajuan Baru</h4>
                <p style="margin-bottom: 25px; color: #64748b; font-size: 13px; line-height: 1.5;">Prosedur pendaftaran sertifikasi kesesuaian SPPT SNI dari tahap awal evaluasi hingga penerbitan sertifikat resmi produk.</p>
            </div>
            <a href="{{ route('pengajuan.buat', ['tahap' => '1']) }}" style="display: block; text-align: center; padding: 12px; background: #198754; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: 0.3s; margin-top: auto;">Pilih Layanan Ini</a>
        </div>

        <div class="info-card" style="border-top: 5px solid #0d6efd; position: relative; overflow: hidden; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position: absolute; top: 10px; right: 15px; color: #f1f5f9; font-size: 45px; font-weight: 800; opacity: 0.15;">F2</div>
            <div>
                <h3 style="color: #0d6efd; margin-bottom: 12px; font-size: 1.25rem; font-weight: 700;">
                    <i class="fa-solid fa-shield-halved"></i> Form 2
                </h3>
                <h4 style="font-size: 14px; color: #334155; font-weight: 600; margin-bottom: 8px;">Survailen</h4>
                <p style="margin-bottom: 25px; color: #64748b; font-size: 13px; line-height: 1.5;">Proses pengawasan berkala dan audit tahunan terhadap fasilitas pabrik yang telah memegang status sertifikasi aktif.</p>
            </div>
            <a href="{{ route('pengajuan.buat', ['tahap' => '2']) }}" style="display: block; text-align: center; padding: 12px; background: #0d6efd; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: 0.3s; margin-top: auto;">Pilih Layanan Ini</a>
        </div>

        <div class="info-card" style="border-top: 5px solid #ffc107; position: relative; overflow: hidden; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position: absolute; top: 10px; right: 15px; color: #f1f5f9; font-size: 45px; font-weight: 800; opacity: 0.15;">F3</div>
            <div>
                <h3 style="color: #b58500; margin-bottom: 12px; font-size: 1.25rem; font-weight: 700;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Form 3
                </h3>
                <h4 style="font-size: 14px; color: #334155; font-weight: 600; margin-bottom: 8px;">Ruang Lingkup</h4>
                <p style="margin-bottom: 25px; color: #64748b; font-size: 13px; line-height: 1.5;">Layanan administrasi terkait pengurangan, perluasan, pembekuan sementara, hingga pencabutan status sertifikat.</p>
            </div>
            <a href="{{ route('pengajuan.buat', ['tahap' => '3']) }}" style="display: block; text-align: center; padding: 12px; background: #ffc107; color: #1e293b; text-decoration: none; border-radius: 10px; font-weight: 600; transition: 0.3s; margin-top: auto;">Pilih Layanan Ini</a>
        </div>

        <div class="info-card" style="border-top: 5px solid #dc3545; position: relative; overflow: hidden; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position: absolute; top: 10px; right: 15px; color: #f1f5f9; font-size: 45px; font-weight: 800; opacity: 0.15;">F4</div>
            <div>
                <h3 style="color: #dc3545; margin-bottom: 12px; font-size: 1.25rem; font-weight: 700;">
                    <i class="fa-solid fa-comments"></i> Form 4
                </h3>
                <h4 style="font-size: 14px; color: #334155; font-weight: 600; margin-bottom: 8px;">Keluhan & Banding</h4>
                <p style="margin-bottom: 25px; color: #64748b; font-size: 13px; line-height: 1.5;">Fasilitas pengaduan resmi atau permohonan banding atas hasil evaluasi kelayakan mutu produk yang diterbitkan oleh sistem.</p>
            </div>
            <a href="{{ route('pengajuan.buat', ['tahap' => '4']) }}" style="display: block; text-align: center; padding: 12px; background: #dc3545; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: 0.3s; margin-top: auto;">Pilih Layanan Ini</a>
        </div>

    </div>

    <div style="margin-top: 30px; background: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 10px; display: flex; align-items: center; gap: 15px;">
        <i class="fa-solid fa-circle-info" style="color: #d97706; font-size: 20px;"></i>
        <p style="color: #92400e; font-size: 13px; margin: 0;">Pastikan data profil perusahaan Anda sudah lengkap sebelum memilih tahap pengajuan untuk hasil dokumen yang maksimal.</p>
    </div>
@endsection