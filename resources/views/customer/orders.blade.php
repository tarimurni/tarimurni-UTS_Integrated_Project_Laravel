@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pesanan Elektronik Saya</h5>
                    <p class="text-muted small mb-0">Daftar transaksi dan struk belanja resmi akun Anda di ElectroMart</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm d-print-none" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak Riwayat
                    </button>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm px-3 d-print-none">
                        <i class="bi bi-bag-plus me-1"></i> Belanja Lagi
                    </a>
                </div>
            </div>

            <div class="card-body px-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No. Invoice</th>
                                <th>Tanggal Pesanan</th>
                                <th>Detail Produk</th>
                                <th class="text-end">Total Pembayaran</th>
                                <th class="text-center" style="width: 140px;">Status Pesanan</th>
                                <th class="text-end d-print-none" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $index => $order)
                                <tr>
                                    <td class="text-muted small">{{ $orders->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $order) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                            {{ $order->sale_number }}
                                        </a>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $order->sold_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <div class="small text-dark">
                                            @foreach($order->items->take(2) as $item)
                                                <div>&bull; {{ $item->product->name ?? 'Produk' }} ({{ $item->quantity }} unit)</div>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <span class="text-muted" style="font-size: 0.75rem;">+{{ $order->items->count() - 2 }} produk lainnya</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($order->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i>Selesai / Lunas
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i>Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end d-print-none">
                                        <a href="{{ route('sales.show', $order) }}" class="btn btn-outline-secondary btn-sm" title="Lihat Struk / Invoice">
                                            <i class="bi bi-receipt me-1"></i> Struk
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-bag-x fs-1 d-block mb-2 text-secondary"></i>
                                        Belum ada riwayat pesanan yang pernah Anda lakukan.<br>
                                        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm mt-3 d-print-none">
                                            <i class="bi bi-cart3 me-1"></i> Mulai Belanja Sekarang
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-print-none">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
