@extends('layouts.app')

@section('title', 'Kasir Penjualan Baru (POS) - ElectroMart')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-11">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-cart-check-fill text-success me-2"></i>Mesin Kasir Penjualan Elektronik (Point of Sales)</h5>
                    <p class="text-muted small mb-0">Input penjualan barang ke pelanggan. Stok inventaris akan berkurang secara otomatis dan atomik.</p>
                </div>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                </a>
            </div>

            <div class="card-body p-4 pt-0">
                <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                    @csrf

                    <!-- Customer & Transaction Header -->
                    <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label fw-semibold small text-secondary">Pelanggan Terdaftar (Opsional)</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                <option value="">-- Pembeli Umum (Walk-in Customer) --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }} - {{ $customer->phone ?? 'Tanpa No. Telp' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Pilih jika pembeli merupakan pelanggan berakun member.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="sold_at" class="form-label fw-semibold small text-secondary">Tanggal & Waktu Transaksi</label>
                            <input type="datetime-local" class="form-control @error('sold_at') is-invalid @enderror" id="sold_at" name="sold_at" value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}">
                            <div class="form-text small">Waktu pencatatan nota transaksi di kasir.</div>
                        </div>
                    </div>

                    <!-- Cart Items Section -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bag-check text-success me-2"></i>Item Barang yang Dibeli</h6>
                        <button type="button" class="btn btn-sm btn-outline-success" id="addRowBtn">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Item Produk
                        </button>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" id="saleItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 340px;">Pilih Produk Elektronik <span class="text-danger">*</span></th>
                                    <th style="width: 130px;" class="text-center">Sisa Stok</th>
                                    <th style="width: 140px;">Kuantitas <span class="text-danger">*</span></th>
                                    <th style="width: 200px;" class="text-end">Harga Jual Satuan (Rp)</th>
                                    <th style="width: 200px;" class="text-end">Subtotal (Rp)</th>
                                    <th style="width: 60px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="saleItemsTableBody">
                                <!-- Initial Item Row -->
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-select form-select-sm product-select" required onchange="handleProductChange(this)">
                                            <option value="">-- Pilih Produk yang Tersedia --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">
                                                    [{{ $product->sku }}] {{ $product->name }} @if($product->color_name) ({{ $product->color_name }}) @endif - Stok: {{ $product->stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary current-stock">-</span>
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="items[0][quantity]" class="form-control form-control-sm text-center qty-input" value="1" required oninput="calculateRow(this)">
                                    </td>
                                    <td class="text-end text-secondary fw-semibold row-price">
                                        Rp 0
                                    </td>
                                    <td class="text-end fw-bold text-success row-subtotal">
                                        Rp 0
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn" onclick="removeRow(this)" title="Hapus Baris">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold fs-6">Grand Total Penjualan:</td>
                                    <td class="text-end fw-bold fs-5 text-success" id="grandTotalDisplay">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Method Section (ElectroPay & Cash) -->
                    <div class="card border-0 bg-light rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-wallet2 text-success me-2"></i>Metode Pembayaran Kasir (ElectroPay Gateway)
                            </h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-shield-check me-1"></i>Otomatis Verifikasi Lunas
                            </span>
                        </div>
                        <p class="text-muted small mb-3">Pilih kanal pembayaran pelanggan. Transaksi langsung tercatat dengan nomor referensi unik Payment Gateway.</p>

                        <div class="row g-2">
                            <!-- Tunai / Cash -->
                            <div class="col-md-4 col-sm-6">
                                <label class="payment-box border rounded-3 p-3 bg-white d-flex align-items-start gap-2 h-100 cursor-pointer shadow-sm position-relative" for="pos_cash" style="cursor: pointer;">
                                    <input class="form-check-input mt-1" type="radio" name="payment_method" id="pos_cash" value="cash" checked>
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-cash-stack text-success fs-6"></i> Tunai / Cash di Toko
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Uang tunai fisik diterima langsung oleh kasir</div>
                                        <span class="badge bg-success text-white mt-1" style="font-size: 0.6rem;">Kasir Offline</span>
                                    </div>
                                </label>
                            </div>

                            <!-- QRIS Instant -->
                            <div class="col-md-4 col-sm-6">
                                <label class="payment-box border rounded-3 p-3 bg-white d-flex align-items-start gap-2 h-100 cursor-pointer shadow-sm" for="pos_qris" style="cursor: pointer;">
                                    <input class="form-check-input mt-1" type="radio" name="payment_method" id="pos_qris" value="qris">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-qr-code-scan text-danger fs-6"></i> QRIS Instant
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Scan QR di layar kasir (BCA, GoPay, OVO, Dana, dll)</div>
                                        <span class="badge bg-danger text-white mt-1" style="font-size: 0.6rem;">Semua Bank & E-Wallet</span>
                                    </div>
                                </label>
                            </div>

                            <!-- BCA VA -->
                            <div class="col-md-4 col-sm-6">
                                <label class="payment-box border rounded-3 p-3 bg-white d-flex align-items-start gap-2 h-100 cursor-pointer shadow-sm" for="pos_va_bca" style="cursor: pointer;">
                                    <input class="form-check-input mt-1" type="radio" name="payment_method" id="pos_va_bca" value="va_bca">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-bank2 text-primary fs-6"></i> BCA Virtual Account
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Transfer m-BCA / ATM BCA nomor VA 16 digit</div>
                                        <span class="badge bg-light text-primary border mt-1" style="font-size: 0.6rem;">BCA</span>
                                    </div>
                                </label>
                            </div>

                            <!-- BSI VA -->
                            <div class="col-md-4 col-sm-6">
                                <label class="payment-box border rounded-3 p-3 bg-white d-flex align-items-start gap-2 h-100 cursor-pointer shadow-sm" for="pos_va_bsi" style="cursor: pointer;">
                                    <input class="form-check-input mt-1" type="radio" name="payment_method" id="pos_va_bsi" value="va_bsi">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-bank fs-6" style="color: #00a39e;"></i> BSI Virtual Account
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Bank Syariah Indonesia (BSI Mobile / ATM BSI)</div>
                                        <span class="badge bg-light border mt-1" style="font-size: 0.6rem; color: #00a39e;">BSI Syariah</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Mandiri VA -->
                            <div class="col-md-4 col-sm-6">
                                <label class="payment-box border rounded-3 p-3 bg-white d-flex align-items-start gap-2 h-100 cursor-pointer shadow-sm" for="pos_va_mandiri" style="cursor: pointer;">
                                    <input class="form-check-input mt-1" type="radio" name="payment_method" id="pos_va_mandiri" value="va_mandiri">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-wallet2 text-warning fs-6"></i> Mandiri Bill / VA
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Pembayaran via Livin' by Mandiri & ATM Mandiri</div>
                                        <span class="badge bg-light text-warning border mt-1" style="font-size: 0.6rem;">Mandiri</span>
                                    </div>
                                </label>
                            </div>

                            <!-- E-Wallet -->
                            <div class="col-md-4 col-sm-6">
                                <label class="payment-box border rounded-3 p-3 bg-white d-flex align-items-start gap-2 h-100 cursor-pointer shadow-sm" for="pos_ewallet" style="cursor: pointer;">
                                    <input class="form-check-input mt-1" type="radio" name="payment_method" id="pos_ewallet" value="ewallet">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-phone-fill text-info fs-6"></i> E-Wallet Instant
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">GoPay / OVO / DANA / ShopeePay</div>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle mt-1" style="font-size: 0.6rem;">E-Wallet</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div class="small text-muted">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i>
                            Stok produk dikunci dengan <code>lockForUpdate</code> untuk mencegah race condition atau overselling.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                                <i class="bi bi-check2-circle me-1"></i> Selesaikan Transaksi Kasir
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let rowIndex = 1;
    const productsData = @json($products);

    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tbody = document.getElementById('saleItemsTableBody');

        let optionsHtml = '<option value="">-- Pilih Produk yang Tersedia --</option>';
        productsData.forEach(p => {
            const colorInfo = p.color_name ? ` (${p.color_name})` : '';
            optionsHtml += `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock}">[${p.sku}] ${p.name}${colorInfo} - Stok: ${p.stock}</option>`;
        });

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required onchange="handleProductChange(this)">
                    ${optionsHtml}
                </select>
            </td>
            <td class="text-center">
                <span class="badge bg-secondary current-stock">-</span>
            </td>
            <td>
                <input type="number" min="1" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center qty-input" value="1" required oninput="calculateRow(this)">
            </td>
            <td class="text-end text-secondary fw-semibold row-price">
                Rp 0
            </td>
            <td class="text-end fw-bold text-success row-subtotal">
                Rp 0
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn" onclick="removeRow(this)" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
        updateTotals();
    });

    function handleProductChange(selectElem) {
        const row = selectElem.closest('tr');
        const selectedOption = selectElem.options[selectElem.selectedIndex];

        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

        row.querySelector('.current-stock').textContent = stock > 0 ? stock + ' unit' : 'Habis';
        row.querySelector('.current-stock').className = stock > 0 ? 'badge bg-success' : 'badge bg-danger';

        const qtyInput = row.querySelector('.qty-input');
        if (stock > 0) {
            qtyInput.max = stock;
        }

        row.querySelector('.row-price').textContent = 'Rp ' + price.toLocaleString('id-ID');
        row.querySelector('.row-price').setAttribute('data-price', price);

        calculateRow(selectElem);
    }

    function calculateRow(elem) {
        const row = elem.closest('tr');
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.row-price').getAttribute('data-price')) || 0;
        const subtotal = qty * price;

        row.querySelector('.row-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateTotals();
    }

    function removeRow(btn) {
        const tbody = document.getElementById('saleItemsTableBody');
        if (tbody.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove();
            updateTotals();
        } else {
            alert('Minimal harus ada 1 baris item produk dalam transaksi penjualan.');
        }
    }

    function updateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.row-price').getAttribute('data-price')) || 0;
            grandTotal += (qty * price);
        });

        document.getElementById('grandTotalDisplay').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }
</script>
@endpush
