@extends('layouts.app')

@section('title', 'Manajemen Pengguna - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-people text-primary me-2"></i>Manajemen Akun Pengguna</h5>
                    <p class="text-muted small mb-0">Kelola pengguna sistem ElectroMart dengan 3 role: Admin, Staff Gudang & Kasir, dan Customer</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
                </a>
            </div>

            <div class="card-body px-4 pt-0">
                <!-- Search & Role Filter -->
                <form action="{{ route('users.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau no telepon..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff Gudang & Kasir</option>
                            <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                        @if(request()->anyFilled(['search', 'role']))
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Alamat Email</th>
                                <th class="text-center" style="width: 120px;">Role</th>
                                <th>No. Telepon</th>
                                <th style="width: 160px;">Terdaftar</th>
                                <th class="text-end" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $item)
                                <tr>
                                    <td class="text-muted small">{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            {{ $item->name }}
                                            @if($item->id === Auth::id())
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">Akun Anda</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $item->address ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="text-secondary small font-monospace">{{ $item->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->isAdmin())
                                            <span class="badge badge-role-admin text-uppercase px-2 py-1">Admin</span>
                                        @elseif($item->isStaff())
                                            <span class="badge badge-role-staff text-uppercase px-2 py-1">Staff</span>
                                        @else
                                            <span class="badge badge-role-customer text-uppercase px-2 py-1">Customer</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $item->phone ?? '-' }}</td>
                                    <td class="small text-muted">{{ $item->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('users.edit', $item) }}" class="btn btn-outline-primary" title="Ubah Pengguna">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($item->id !== Auth::id())
                                                <form action="{{ route('users.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Pengguna">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada akun pengguna ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
