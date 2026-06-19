@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('extra-css')
<style>
    .profile-wrapper {
        max-width: 960px;
        margin: 24px auto;
        padding: 0 16px;
    }
    .profile-page-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .profile-back-btn {
        text-decoration: none;
        color: #16a34a;
        font-size: 20px;
        transition: transform 0.2s;
        line-height: 1;
    }
    .profile-back-btn:hover { transform: translateX(-3px); color: #15803d; }

    .profile-card {
        background: #ffffff;
        padding: 40px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    /* === Header Bar === */
    .profile-header-bar {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 28px;
    }
    .avatar-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        flex-shrink: 0;
    }
    .avatar-client  { background: linear-gradient(135deg, #16a34a, #4ade80); }
    .avatar-admin   { background: linear-gradient(135deg, #2563eb, #60a5fa); }
    .avatar-superadmin { background: linear-gradient(135deg, #7c3aed, #a78bfa); }

    .profile-name { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .profile-role-badge {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .badge-client     { background: #dcfce7; color: #166534; }
    .badge-admin      { background: #dbeafe; color: #1d4ed8; }
    .badge-superadmin { background: #ede9fe; color: #6d28d9; }

    /* === Form Grid === */
    .profile-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    .span-2 { grid-column: span 2; }

    .profile-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 7px;
    }
    .profile-field input,
    .profile-field textarea,
    .profile-field select {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        color: #1e293b;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .profile-field input:focus,
    .profile-field textarea:focus {
        border-color: #16a34a;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
    }
    .profile-field textarea { resize: vertical; }

    /* Email change notice */
    .email-notice {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        background: #fefce8;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 6px;
        font-size: 12px;
        color: #92400e;
    }

    /* Section label */
    .section-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    /* Buttons */
    .btn-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-save {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        color: white;
        border: none;
        padding: 11px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 11px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .btn-cancel:hover { background: #e2e8f0; color: #1e293b; }

    /* Alerts */
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .alert-error ul { margin: 6px 0 0 0; padding-left: 18px; }

    @media (max-width: 640px) {
        .profile-form-grid { grid-template-columns: 1fr; }
        .span-2 { grid-column: span 1; }
        .profile-card { padding: 24px 18px; }
        .profile-header-bar { flex-direction: column; text-align: center; }
    }
</style>
@endsection

@section('content')
@php
    $userRole = strtolower($user->role ?? 'client');
    $isSuperadmin = ($userRole === 'superadmin');
    $isInternal   = in_array($userRole, ['superadmin', 'admin']);

    // Avatar & badge class
    if ($isSuperadmin) {
        $avatarClass = 'avatar-superadmin';
        $badgeClass  = 'badge-superadmin';
        $badgeText   = 'SUPERADMIN';
        $backRoute   = route('admin.dashboard');
    } elseif ($isInternal) {
        $avatarClass = 'avatar-admin';
        $badgeClass  = 'badge-admin';
        $subRoleLabel = $user->unit_kerja ?? (strtoupper($user->sub_role ?? 'ADMIN'));
        $badgeText   = 'ADMIN – ' . $subRoleLabel;
        $backRoute   = route('admin.dashboard');
    } else {
        $avatarClass = 'avatar-client';
        $badgeClass  = 'badge-client';
        $badgeText   = 'CLIENT';
        $backRoute   = route('client.dashboard');
    }

    // Primary display name
    $displayName = $user->nama_penghubung ?? $user->email;
@endphp

<div class="profile-wrapper">

    <div class="profile-page-header">
        <a href="{{ $backRoute }}" class="profile-back-btn" title="Kembali ke Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="color: #1e293b; margin: 0; font-size: 22px; font-weight: 700;">Pengaturan Profil</h2>
            <p style="color: #64748b; margin: 4px 0 0 0; font-size: 13px;">Perbarui informasi akun Anda yang terdaftar di sistem.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <div style="font-weight: 700; margin-bottom: 4px;">Gagal menyimpan perubahan:</div>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-card">

        {{-- === HEADER: Avatar & Identity === --}}
        <div class="profile-header-bar">
            <div class="avatar-circle {{ $avatarClass }}">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <p class="profile-name">{{ $displayName }}</p>
                @if(!$isInternal && $user->nama_perusahaan)
                    <p style="margin: 0 0 6px 0; font-size: 13px; color: #475569; font-weight: 500;">
                        {{ $user->nama_perusahaan }}
                    </p>
                @endif
                @if($isInternal && $user->jabatan)
                    <p style="margin: 0 0 6px 0; font-size: 13px; color: #475569; font-weight: 500;">
                        {{ $user->jabatan }}
                    </p>
                @endif
                <span class="profile-role-badge {{ $badgeClass }}">{{ $badgeText }}</span>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            @if($isInternal)
                {{-- ======================================================== --}}
                {{-- FORM ADMIN / SUPERADMIN                                   --}}
                {{-- ======================================================== --}}
                <div class="section-label">Informasi Pribadi</div>
                <div class="profile-form-grid">

                    <div class="profile-field">
                        <label for="nama_penghubung">Nama Lengkap <span style="color:#ef4444">*</span></label>
                        <input type="text" id="nama_penghubung" name="nama_penghubung"
                               value="{{ old('nama_penghubung', $user->nama_penghubung) }}"
                               placeholder="Masukkan nama lengkap Anda" required>
                    </div>

                    <div class="profile-field">
                        <label for="nip">NIP Pegawai</label>
                        <input type="text" id="nip" name="nip"
                               value="{{ old('nip', $user->nip) }}"
                               placeholder="Nomor Induk Pegawai (opsional)">
                    </div>

                    <div class="profile-field">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" id="jabatan" name="jabatan"
                               value="{{ old('jabatan', $user->jabatan) }}"
                               placeholder="Contoh: Kepala Bidang / Staf TU">
                    </div>

                    <div class="profile-field">
                        <label for="unit_kerja">Unit Kerja / Bidang</label>
                        <input type="text" id="unit_kerja" name="unit_kerja"
                               value="{{ old('unit_kerja', $user->unit_kerja) }}"
                               placeholder="Contoh: Tata Usaha, Seksi Layanan, BRMP SDLP">
                    </div>

                    <div class="profile-field span-2">
                        <label for="email">Alamat Email Resmi <span style="color:#ef4444">*</span></label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               placeholder="nama@pertanian.go.id" required>
                        <div class="email-notice">
                            <i class="fa-solid fa-triangle-exclamation" style="margin-top:1px; flex-shrink:0;"></i>
                            <span>Mengubah email akan mengirim verifikasi ke email baru Anda dan memerlukan verifikasi ulang.</span>
                        </div>
                    </div>

                </div>

            @else
                {{-- ======================================================== --}}
                {{-- FORM CLIENT / VENDOR PERUSAHAAN                           --}}
                {{-- ======================================================== --}}
                <div class="section-label">Informasi Perusahaan</div>
                <div class="profile-form-grid">

                    <div class="profile-field span-2">
                        <label for="nama_perusahaan">Nama Perusahaan / Lembaga <span style="color:#ef4444">*</span></label>
                        <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                               value="{{ old('nama_perusahaan', $user->nama_perusahaan) }}"
                               placeholder="Masukkan nama resmi perusahaan Anda" required>
                    </div>

                </div>

                <div class="section-label" style="margin-top: 20px;">Informasi Kontak (PIC)</div>
                <div class="profile-form-grid">

                    <div class="profile-field">
                        <label for="nama_penghubung">Nama PIC / Contact Person <span style="color:#ef4444">*</span></label>
                        <input type="text" id="nama_penghubung" name="nama_penghubung"
                               value="{{ old('nama_penghubung', $user->nama_penghubung) }}"
                               placeholder="Nama lengkap penghubung" required>
                    </div>

                    <div class="profile-field">
                        <label for="no_telp">Nomor Telepon / WhatsApp <span style="color:#ef4444">*</span></label>
                        <input type="text" id="no_telp" name="no_telp"
                               value="{{ old('no_telp', $user->no_telp) }}"
                               placeholder="Contoh: 0812-3456-7890" required>
                    </div>

                    <div class="profile-field span-2">
                        <label for="email">Alamat Email Kontak <span style="color:#ef4444">*</span></label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               placeholder="nama@perusahaan.com" required>
                        <div class="email-notice">
                            <i class="fa-solid fa-triangle-exclamation" style="margin-top:1px; flex-shrink:0;"></i>
                            <span>Mengubah email akan memerlukan verifikasi ulang ke email baru Anda.</span>
                        </div>
                    </div>

                    <div class="profile-field span-2">
                        <label for="alamat">Alamat Lengkap Kantor <span style="color:#ef4444">*</span></label>
                        <textarea id="alamat" name="alamat" rows="3"
                                  placeholder="Masukkan alamat lengkap perusahaan..." required>{{ old('alamat', $user->alamat) }}</textarea>
                    </div>

                </div>
            @endif

            <div class="btn-actions">
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ $backRoute }}" class="btn-cancel">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection