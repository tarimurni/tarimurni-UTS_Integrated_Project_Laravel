@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name . ' - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </span>
                    <span class="badge {{ $product->stock <= $product->minimum_stock ? 'bg-danger' : 'bg-success' }} px-3 py-2 fw-bold">
                        Stok: {{ $product->stock }} unit
                    </span>
                </div>

                <div class="text-muted small font-monospace mb-1">SKU: {{ $product->sku }}</div>
                <h4 class="fw-bold text-dark mb-2">{{ $product->name }}</h4>
                <div class="mb-3">
                    <span class="badge bg-light text-dark border">{{ $product->category->name ?? '-' }}</span>
                    @if($product->brand)
                        <span class="badge bg-light text-secondary border">Brand: {{ $product->brand }}</span>
                    @endif
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Harga Beli (Modal):</span>
                    <span class="text-secondary fw-semibold">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Harga Jual Konsumen:</span>
                    <span class="text-primary fw-bold fs-5">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Margin Keuntungan:</span>
                    <span class="text-success fw-bold">
                        Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}
                        ({{ $product->purchase_price > 0 ? number_format((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100, 1) : 0 }}%)
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted small">Batas Minimum Stok:</span>
                    <span class="text-muted fw-bold">{{ $product->minimum_stock }} unit</span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-pencil me-1"></i> Ubah Produk
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-down-left-circle text-primary me-2"></i>Riwayat Pembelian Barang Masuk (Restock)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Faktur</th>
                            <th>Distributor</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Biaya Per Unit</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->purchaseItems as $item)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-secondary small">{{ $item->purchase->purchase_number ?? '-' }}</td>
                                <td>{{ $item->purchase->supplier->name ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Belum ada catatan pembelian masuk untuk produk ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack text-success me-2"></i>Riwayat Penjualan Barang Keluar</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Transaksi</th>
                            <th>Kasir / Petugas</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->saleItems as $item)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-secondary small">{{ $item->sale->sale_number ?? '-' }}</td>
                                <td>{{ $item->sale->user->name ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-bold text-success">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Belum ada catatan penjualan keluar untuk produk ini.
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
