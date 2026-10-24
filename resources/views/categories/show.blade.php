@extends('layouts.app')

@section('title', 'Detail Kategori: ' . $category->name . ' - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3 mb-3">
                    <i class="bi bi-tag-fill fs-4"></i>
                </span>
                <h4 class="fw-bold text-dark mb-1">{{ $category->name }}</h4>
                <p class="text-muted small mb-3">{{ $category->description ?? 'Tidak ada deskripsi.' }}</p>

                <hr class="my-3">

                <div class="d-flex justify-content-between text-muted small mb-2">
                    <span>Total Produk:</span>
                    <strong class="text-dark">{{ $category->products->count() }} item</strong>
                </div>
                <div class="d-flex justify-content-between text-muted small mb-3">
                    <span>Terdaftar Pada:</span>
                    <strong class="text-dark">{{ $category->created_at->format('d M Y, H:i') }}</strong>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-pencil me-1"></i> Ubah
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Daftar Produk dalam Kategori Ini</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">SKU</th>
                            <th>Nama Produk</th>
                            <th>Brand</th>
                            <th>Harga Jual</th>
                            <th class="text-center pe-4">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($category->products as $product)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-secondary small">{{ $product->sku }}</td>
                                <td class="fw-semibold text-dark">{{ $product->name }}</td>
                                <td>{{ $product->brand ?? '-' }}</td>
                                <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge {{ $product->stock <= $product->minimum_stock ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                        {{ $product->stock }} unit
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Belum ada produk yang terdaftar pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
