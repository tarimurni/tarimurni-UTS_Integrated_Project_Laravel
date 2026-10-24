@extends('layouts.app')

@section('title', 'Supplier & Distributor - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-truck text-primary me-2"></i>Daftar Pemasok & Distributor</h5>
                    <p class="text-muted small mb-0">Kelola data mitra distributor resmi komponen dan perangkat elektronik</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Supplier
                    </a>
                @endif
            </div>

            <div class="card-body px-4 pt-0">
                <!-- Search Filter Form -->
                <form action="{{ route('suppliers.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau telepon supplier..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Distributor</th>
                                <th>Kontak Telepon</th>
                                <th>Alamat Email</th>
                                <th class="text-center" style="width: 140px;">Total Pembelian</th>
                                <th class="text-end" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $index => $supplier)
                                <tr>
                                    <td class="text-muted small">{{ $suppliers->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $supplier->name }}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $supplier->address ?? 'Alamat tidak dicatat' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="bi bi-telephone me-1"></i>{{ $supplier->phone }}</span>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $supplier->email }}" class="text-decoration-none small text-secondary">
                                            <i class="bi bi-envelope me-1"></i>{{ $supplier->email }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $supplier->purchases_count }} faktur</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary" title="Ubah Supplier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Supplier">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada supplier ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $suppliers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
