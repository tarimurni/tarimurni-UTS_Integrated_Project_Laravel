@extends('layouts.app')

@section('title', 'Catat Pembelian Baru (Restock) - ElectroMart')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-11">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-cart-plus text-primary me-2"></i>Catat Transaksi Pembelian Barang Masuk (Restock)</h5>
                    <p class="text-muted small mb-0">Input faktur pembelian dari distributor. Stok produk akan bertambah secara atomik di database.</p>
                </div>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>

            <div class="card-body p-4 pt-0">
                <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                    @csrf

                    <!-- Supplier & Date Header -->
                    <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                        <div class="col-md-6">
                            <label for="supplier_id" class="form-label fw-semibold small text-secondary">Pilih Distributor / Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} ({{ $supplier->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="purchased_at" class="form-label fw-semibold small text-secondary">Tanggal & Waktu Transaksi</label>
                            <input type="datetime-local" class="form-control @error('purchased_at') is-invalid @enderror" id="purchased_at" name="purchased_at" value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check text-primary me-2"></i>Daftar Item Barang Masuk</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Produk
                        </button>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 320px;">Produk Elektronik <span class="text-danger">*</span></th>
                                    <th style="width: 130px;" class="text-center">Stok Gudang</th>
                                    <th style="width: 140px;">Kuantitas <span class="text-danger">*</span></th>
                                    <th style="width: 200px;">Biaya Satuan (Rp) <span class="text-danger">*</span></th>
                                    <th style="width: 200px;" class="text-end">Subtotal (Rp)</th>
                                    <th style="width: 60px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Initial Item Row -->
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-select form-select-sm product-select" required onchange="handleProductChange(this)">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}" data-stock="{{ $product->stock }}">
                                                    [{{ $product->sku }}] {{ $product->name }} {{ $product->brand ? "({$product->brand})" : '' }}
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
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" step="0.01" min="0" name="items[0][unit_cost]" class="form-control price-input" value="0" required oninput="calculateRow(this)">
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold row-subtotal">
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
                                    <td colspan="4" class="text-end fw-bold fs-6">Grand Total Pembelian:</td>
                                    <td class="text-end fw-bold fs-5 text-primary" id="grandTotalDisplay">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div class="small text-muted">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i>
                            Transaksi diproses secara atomik dengan Pessimistic Locking untuk menjaga akurasi stok inventaris.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Transaksi Pembelian
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

    // Available products data for dynamic rows
    const productsData = @json($products);

    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsTableBody');

        let optionsHtml = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            optionsHtml += `<option value="${p.id}" data-price="${p.purchase_price}" data-stock="${p.stock}">[${p.sku}] ${p.name} ${p.brand ? '(' + p.brand + ')' : ''}</option>`;
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
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_cost]" class="form-control price-input" value="0" required oninput="calculateRow(this)">
                </div>
            </td>
            <td class="text-end fw-bold row-subtotal">
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

        const price = selectedOption.getAttribute('data-price') || 0;
        const stock = selectedOption.getAttribute('data-stock') || '-';

        row.querySelector('.current-stock').textContent = stock !== '-' ? stock + ' unit' : '-';
        row.querySelector('.price-input').value = parseFloat(price);

        calculateRow(selectElem);
    }

    function calculateRow(elem) {
        const row = elem.closest('tr');
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;

        row.querySelector('.row-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateTotals();
    }

    function removeRow(btn) {
        const tbody = document.getElementById('itemsTableBody');
        if (tbody.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove();
            updateTotals();
        } else {
            alert('Minimal harus ada 1 baris item produk dalam faktur pembelian.');
        }
    }

    function updateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            grandTotal += (qty * price);
        });

        document.getElementById('grandTotalDisplay').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }
</script>
@endpush
