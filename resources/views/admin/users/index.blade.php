@extends('layouts.app')

@section('title', 'Manajemen Petugas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-users-gear me-2 text-primary"></i>Manajemen Petugas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa-solid fa-plus me-1"></i> Tambah Petugas
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Petugas</th>
                            <th>Email</th>
                            <th>Role / Divisi</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $user->nama_penghubung }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->sub_role === 'tatausaha')
                                    <span class="badge bg-primary">Tata Usaha</span>
                                @elseif($user->sub_role === 'layanan')
                                    <span class="badge bg-info">Layanan</span>
                                @elseif($user->sub_role === 'audit')
                                    <span class="badge bg-warning text-dark">Audit</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                        Opsi <i class="fa-solid fa-chevron-down ms-1" style="font-size: 10px;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}">
                                                <i class="fa-solid fa-key me-2 text-warning"></i> Reset Password
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                        <li>
                                            <form action="{{ route('superadmin.users.toggle_status', $user) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-{{ $user->is_active ? 'warning' : 'success' }}">
                                                    <i class="fa-solid fa-power-off me-2"></i> {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus petugas ini? Data terkait mungkin akan ikut terhapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data petugas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modals for Edit and Reset Password --}}
@foreach($users as $user)
    {{-- Modal Edit User --}}
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('superadmin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Petugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Petugas</label>
                        <input type="text" name="nama_penghubung" class="form-control" value="{{ $user->nama_penghubung }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role / Divisi</label>
                        <select name="sub_role" class="form-select" required>
                            <option value="tatausaha" {{ $user->sub_role == 'tatausaha' ? 'selected' : '' }}>Tata Usaha</option>
                            <option value="layanan" {{ $user->sub_role == 'layanan' ? 'selected' : '' }}>Layanan</option>
                            <option value="audit" {{ $user->sub_role == 'audit' ? 'selected' : '' }}>Audit</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Reset Password --}}
    <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form class="modal-content" action="{{ route('superadmin.users.reset_password', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100">Reset</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Modal Add User --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('superadmin.users.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Petugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Petugas</label>
                    <input type="text" name="nama_penghubung" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role / Divisi</label>
                    <select name="sub_role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="tatausaha">Tata Usaha</option>
                        <option value="layanan">Layanan</option>
                        <option value="audit">Audit</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Petugas</button>
            </div>
        </form>
    </div>
</div>
@endsection
