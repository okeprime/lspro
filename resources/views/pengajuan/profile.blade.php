@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('extra-css')
<style>
    .profile-card {
        background: white;
        padding: 40px;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .profile-header-info {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }
    .avatar-circle {
        width: 80px;
        height: 80px;
        background: #16a34a;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }
    .profile-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .profile-group {
        margin-bottom: 15px;
    }
    .profile-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #64748b;
    }
    .profile-group input, .profile-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
    }
    .profile-group input:focus, .profile-group textarea:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    .btn-group-profile {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    .btn-submit-profile {
        background: #16a34a;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-submit-profile:hover {
        background: #15803d;
    }
    .btn-batal-profile {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .btn-batal-profile:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    @media (max-width: 768px) {
        .profile-form-grid {
            grid-template-columns: 1fr;
        }
        .profile-header-info {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 900px; margin: 20px auto; padding: 0 10px;">
    
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
        <a href="{{ route($dashboard_route) }}" style="text-decoration: none; color: #16a34a; font-size: 20px; transition: 0.2s;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="color: #1e293b; margin: 0; font-size: 24px; font-weight: 700;">Pengaturan Profil</h2>
            <p style="color: #64748b; margin: 5px 0 0 0; font-size: 14px;">Perbarui data informasi akun Anda yang terdaftar di dalam sistem.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
            <div style="font-weight: 700; margin-bottom: 5px;">Gagal menyimpan perubahan:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-card">
        <div class="profile-header-info">
            <div class="avatar-circle">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <h3 style="font-size: 20px; color: #1e293b; margin: 0; font-weight: 700;">
                    {{ $user->name }}
                </h3>
                
                @if(!in_array(strtolower(trim($user->role ?? 'client')), ['superadmin', 'admin', 'tu', 'tata_usaha', 'teknis', 'pimpinan']))
                    <p style="color: #475569; font-size: 14px; margin: 4px 0 6px 0; font-weight: 500;">
                        {{ $user->nama_perusahaan ?? 'Nama Perusahaan Belum Diatur' }}
                    </p>
                @else
                    <p style="color: #475569; font-size: 14px; margin: 4px 0 6px 0; font-weight: 500;">
                        {{ $user->departemen ?? 'Kementerian Pertanian' }}
                    </p>
                @endif

                <p style="color: #64748b; font-size: 13px; margin: 0; font-weight: 500;">
                    Role: <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid #e2e8f0; margin-left: 2px;">{{ $role }}</span>
                </p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="profile-form-grid">
                
                {{-- BLOK 1: Jika User login sebagai jajaran PEGAWAI INTERNAL / ADMIN --}}
                @if(in_array(strtolower(trim($user->role ?? 'client')), ['superadmin', 'admin', 'tu', 'tata_usaha', 'teknis', 'pimpinan']))
                    
                    <div class="profile-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="profile-group">
                        <label for="nip">NIP Pegawai</label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="Masukkan nomor NIP (opsional)">
                    </div>

                    <div class="profile-group">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Masukkan nama jabatan">
                    </div>

                    <div class="profile-group">
                        <label for="departemen">Departemen / Bidang Kerja</label>
                        <input type="text" id="departemen" name="departemen" value="{{ old('departemen', $user->departemen) }}" placeholder="Contoh: Tata Usaha / Tim Teknis">
                    </div>

                    <div class="profile-group" style="grid-column: span 2;">
                        <label for="email">Alamat Email Resmi</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="nama@email.com">
                    </div>

                {{-- BLOK 2: Jika User login sebagai KLIEN / VENDOR PERUSAHAAN LUAR --}}
                @else
                    
                    <div class="profile-group">
                        <label for="nama_perusahaan">Nama Perusahaan / Lembaga Pemohon</label>
                        <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan', $user->nama_perusahaan) }}" placeholder="Masukkan nama resmi perusahaan">
                    </div>

                    <div class="profile-group">
                        <label for="nama_penghubung">Nama Personil Penghubung (Contact Person)</label>
                        <input type="text" id="nama_penghubung" name="nama_penghubung" value="{{ old('nama_penghubung', $user->nama_penghubung) }}" placeholder="Masukkan nama penghubung">
                    </div>

                    <div class="profile-group">
                        <label for="no_telp">Nomor Telepon / WhatsApp Kontak</label>
                        <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" placeholder="Contoh: 0812345678xx">
                    </div>

                    <div class="profile-group">
                        <label for="email">Alamat Email Kontak Akun</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="nama@perusahaan.com">
                    </div>

                    <div class="profile-group" style="grid-column: span 2;">
                        <label for="alamat">Alamat Lengkap Kantor Perusahaan</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap korespondensi kantor..." style="resize: none;">{{ old('alamat', $user->alamat) }}</textarea>
                    </div>

                @endif

            </div>

            <div class="btn-group-profile">
                <button type="submit" class="btn-submit-profile">
                    <i class="fa-solid fa-floppy-disk" style="margin-right: 5px;"></i> Simpan Perubahan
                </button>
                <a href="{{ route($dashboard_route) }}" class="btn-batal-profile">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection