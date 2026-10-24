@extends('layouts.app')

@section('title', 'Katalog & Inventaris Produk - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Katalog & Inventaris Produk Elektronik</h5>
                    <p class="text-muted small mb-0">Manajemen stok barang, harga beli, harga jual, dan status ketersediaan</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Produk Baru
                    </a>
                @endif
            </div>

            <div class="card-body px-4 pt-0">
                <!-- Search & Filters -->
                <form action="{{ route('products.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, SKU, atau brand..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        @if(request('filter') === 'low_stock')
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-danger active">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Filter: Stok Menipis (Aktif)
                            </a>
                        @else
                            <a href="{{ route('products.index', array_merge(request()->query(), ['filter' => 'low_stock'])) }}" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-funnel me-1"></i> Tampilkan Stok Menipis
                            </a>
                        @endif
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" class="btn btn-sm btn-secondary">Terapkan Filter</button>
                        @if(request()->anyFilled(['search', 'category_id', 'filter']))
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 130px;">SKU</th>
                                <th>Nama Produk & Brand</th>
                                <th>Kategori</th>
                                <th class="text-end">Harga Beli</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-center" style="width: 100px;">Stok</th>
                                <th class="text-center" style="width: 90px;">Min</th>
                                <th class="text-end" style="width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $product)
                                <tr class="{{ $product->stock <= $product->minimum_stock ? 'table-warning bg-opacity-25' : '' }}">
                                    <td class="text-muted small">{{ $products->firstItem() + $index }}</td>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary small">{{ $product->sku }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        @if($product->brand)
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                                Brand: {{ $product->brand }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $product->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($product->stock <= $product->minimum_stock)
                                            <span class="badge bg-danger px-2 py-1" title="Stok kritis di bawah minimum!">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $product->stock }}
                                            </span>
                                        @else
                                            <span class="badge bg-success px-2 py-1">
                                                {{ $product->stock }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted small fw-semibold">
                                        {{ $product->minimum_stock }}
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary" title="Detail Produk">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary" title="Ubah Produk">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/mengarsipkan produk ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus / Arsipkan Produk">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada produk ditemukan sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
