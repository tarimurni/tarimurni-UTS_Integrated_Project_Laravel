@extends('layouts.app')

@section('title', 'Kategori Produk - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-tags text-primary me-2"></i>Daftar Kategori Produk</h5>
                    <p class="text-muted small mb-0">Kelola kategori barang untuk pengelompokan produk elektronik</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
                    </a>
                @endif
            </div>

            <div class="card-body px-4 pt-0">
                <!-- Search Filter Form -->
                <form action="{{ route('categories.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau deskripsi kategori..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 140px;">Jumlah Produk</th>
                                <th class="text-end" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                                <tr>
                                    <td class="text-muted small">{{ $categories->firstItem() + $index }}</td>
                                    <td class="fw-bold text-dark">{{ $category->name }}</td>
                                    <td class="text-muted small text-truncate" style="max-width: 300px;">{{ $category->description ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $category->products_count }} item</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-secondary" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary" title="Ubah Kategori">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Kategori">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada kategori ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
