@extends('layouts.app')

@section('title', 'Katalog Produk Elektronik - ElectroMart')

@section('content')
<div class="row g-4">

    <!-- Store Banner -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-primary px-3 py-2 text-uppercase mb-2">Katalog Resmi & Bergaransi</span>
                    <h3 class="fw-bold mb-1">Belanja Elektronik Mudah di ElectroMart</h3>
                    <p class="text-secondary mb-0">Temukan berbagai pilihan perangkat elektronik resmi: Smart TV, Kulkas, AC, Mesin Cuci, Laptop, dan Smartphone dengan tampilan warna asli serta stok real-time.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @auth
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('customer.orders') }}" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-clock-history me-1"></i> Riwayat Pesanan Saya
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <form action="{{ route('catalog.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama produk, SKU, atau brand..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori Produk</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="stock_status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status Stok</option>
                        <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Stok Tersedia</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Stok Rendah (&le; Min)</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Stok Habis (0)</option>
                    </select>
                </div>
                <div class="col-auto ms-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3"><i class="bi bi-funnel me-1"></i>Filter</button>
                    @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
                        <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid (8 cols) & Shopping Cart / Checkout (4 cols) -->
    <div class="col-lg-8">
        <div class="row g-3">
            @forelse($products as $product)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-3 d-flex flex-column justify-content-between position-relative">
                        <div>
                            <div class="position-relative mb-3 overflow-hidden rounded-3 bg-light">
                                <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=800&q=80' }}"
                                     alt="{{ $product->name }}"
                                     class="card-img-top rounded-3"
                                     style="object-fit: cover; height: 200px; width: 100%;"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&w=800&q=80';">
                                <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white small">
                                    {{ $product->category->name ?? 'Elektronik' }}
                                </span>
                                <span class="position-absolute top-0 end-0 m-2 badge {{ $product->stock <= $product->minimum_stock ? 'bg-danger' : 'bg-success' }}">
                                    Stok: {{ $product->stock }} unit
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small font-monospace">{{ $product->sku }}</span>
                                @if($product->brand)
                                    <span class="badge bg-light text-secondary border" style="font-size: 0.75rem;">
                                        {{ $product->brand }}
                                    </span>
                                @endif
                            </div>

                            <h6 class="fw-bold text-dark mb-2" title="{{ $product->name }}" style="min-height: 40px;">{{ $product->name }}</h6>

                            @if($product->color_name)
                                <div class="d-flex align-items-center gap-2 mb-2 p-1 px-2 rounded-2 bg-light border" style="width: fit-content;">
                                    <span style="display:inline-block; width:14px; height:14px; border-radius:50%; background-color: {{ $product->color_hex ?? '#ccc' }}; border: 1px solid #ccc; flex-shrink: 0;" title="{{ $product->color_hex }}"></span>
                                    <span class="small text-muted" style="font-size: 0.8rem;">Warna: <strong class="text-dark">{{ $product->color_name }}</strong></span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-3 border-top mt-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="small text-muted d-block" style="font-size: 0.75rem;">Harga Resmi:</span>
                                <span class="fw-bold text-primary fs-5">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }})">
                                <i class="bi bi-cart-plus me-1"></i> Beli
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center border-0 shadow-sm">
                        <i class="bi bi-search fs-1 text-muted mb-2"></i>
                        <h5 class="fw-bold text-dark">Tidak ada produk ditemukan</h5>
                        <p class="text-muted mb-0">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Checkout Cart Panel (4 cols) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bag-check-fill text-primary me-2"></i>Keranjang Checkout Anda</h6>
                <span class="badge bg-primary rounded-pill" id="cartCountBadge">0 item</span>
            </div>

            <div class="card-body p-4 pt-0">
                <form action="{{ route('catalog.checkout') }}" method="POST" id="checkoutForm">
                    @csrf

                    <!-- Dynamic Cart Items Container -->
                    <div id="cartItemsList" class="mb-3" style="max-height: 380px; overflow-y: auto;">
                        <div class="text-center py-4 text-muted small" id="emptyCartMessage">
                            <i class="bi bi-cart-x fs-2 d-block mb-1 text-secondary"></i>
                            Keranjang belanja Anda masih kosong.<br>Klik tombol <strong>Beli</strong> pada produk pilihan Anda.
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Total Pembayaran:</span>
                            <span class="fw-bold text-dark fs-5" id="cartGrandTotal">Rp 0</span>
                        </div>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i>Harga sudah termasuk PPN & garansi resmi.
                        </div>
                    </div>

                    <!-- Customer Information for Order -->
                    <div class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark small mb-0">
                                <i class="bi bi-person-fill text-primary me-1"></i> Data Pembeli
                            </h6>
                            @guest
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">Tanpa Login</span>
                            @endguest
                        </div>

                        <div class="mb-2">
                            <label for="customer_name" class="form-label small fw-semibold text-secondary mb-1">
                                Nama Pelanggan <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="customer_name"
                                   id="customer_name"
                                   class="form-control form-control-sm @error('customer_name') is-invalid @enderror"
                                   placeholder="Contoh: Rahmania"
                                   value="{{ old('customer_name', Auth::check() ? Auth::user()->name : '') }}"
                                   required>
                            @error('customer_name')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_phone" class="form-label small fw-semibold text-secondary mb-1">
                                Nomor HP / WhatsApp <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="text"
                                   name="customer_phone"
                                   id="customer_phone"
                                   class="form-control form-control-sm @error('customer_phone') is-invalid @enderror"
                                   placeholder="0812xxxx (Opsional)"
                                   value="{{ old('customer_phone', Auth::check() ? Auth::user()->phone : '') }}">
                        </div>
                    </div>

                    <!-- Payment Gateway Selection -->
                    <div class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark small mb-0">
                                <i class="bi bi-shield-check text-primary me-1"></i> Metode Pembayaran
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                                <i class="bi bi-lightning-charge-fill me-1"></i>ElectroPay
                            </span>
                        </div>
                        <div class="text-muted small mb-2" style="font-size: 0.72rem;">
                            Didukung Midtrans Snap Gateway dengan verifikasi otomatis instan.
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <!-- QRIS -->
                            <label class="border rounded-2 p-2 bg-white d-flex align-items-center justify-content-between mb-0" for="cat_pay_qris" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" id="cat_pay_qris" value="qris" checked>
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-qr-code-scan text-danger"></i> QRIS Instant
                                            <span class="badge bg-danger text-white ms-1" style="font-size: 0.58rem;">Semua Bank & E-Wallet</span>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Scan QR via BCA, Mandiri, BRI, BSI, GoPay, OVO, Dana</div>
                                    </div>
                                </div>
                            </label>

                            <!-- VA BCA -->
                            <label class="border rounded-2 p-2 bg-white d-flex align-items-center justify-content-between mb-0" for="cat_pay_va_bca" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" id="cat_pay_va_bca" value="va_bca">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-bank2 text-primary"></i> BCA Virtual Account
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Transfer otomatis via m-BCA, KlikBCA & ATM BCA</div>
                                    </div>
                                </div>
                                <span class="badge bg-light text-primary border" style="font-size: 0.6rem;">BCA</span>
                            </label>

                            <!-- VA BSI -->
                            <label class="border rounded-2 p-2 bg-white d-flex align-items-center justify-content-between mb-0" for="cat_pay_va_bsi" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" id="cat_pay_va_bsi" value="va_bsi">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-bank text-teal" style="color: #00a39e;"></i> BSI Virtual Account
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Bank Syariah Indonesia (BSI Mobile & ATM)</div>
                                    </div>
                                </div>
                                <span class="badge bg-light border" style="font-size: 0.6rem; color: #00a39e;">BSI</span>
                            </label>

                            <!-- VA Mandiri -->
                            <label class="border rounded-2 p-2 bg-white d-flex align-items-center justify-content-between mb-0" for="cat_pay_va_mandiri" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" id="cat_pay_va_mandiri" value="va_mandiri">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-wallet2 text-warning"></i> Mandiri Bill / VA
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Pembayaran via Livin' by Mandiri & ATM Mandiri</div>
                                    </div>
                                </div>
                                <span class="badge bg-light text-warning border" style="font-size: 0.6rem;">Mandiri</span>
                            </label>

                            <!-- E-Wallet -->
                            <label class="border rounded-2 p-2 bg-white d-flex align-items-center justify-content-between mb-0" for="cat_pay_ewallet" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" id="cat_pay_ewallet" value="ewallet">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <i class="bi bi-phone-fill text-info"></i> E-Wallet Instant
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">GoPay / OVO / DANA / ShopeePay</div>
                                    </div>
                                </div>
                                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.6rem;">E-Wallet</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold" id="checkoutBtn" disabled>
                        <i class="bi bi-credit-card-2-front me-1"></i> Proses Checkout Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const cart = {};

    function addToCart(id, name, price, maxStock) {
        if (!cart[id]) {
            cart[id] = { id: id, name: name, price: price, maxStock: maxStock, quantity: 1 };
        } else {
            if (cart[id].quantity < maxStock) {
                cart[id].quantity++;
            } else {
                alert(`Maksimal stok yang tersedia untuk produk ini adalah ${maxStock} unit.`);
            }
        }
        renderCart();
    }

    function updateQty(id, delta) {
        if (cart[id]) {
            const newQty = cart[id].quantity + delta;
            if (newQty > cart[id].maxStock) {
                alert(`Maksimal stok yang tersedia untuk produk ini adalah ${cart[id].maxStock} unit.`);
            } else if (newQty <= 0) {
                delete cart[id];
            } else {
                cart[id].quantity = newQty;
            }
        }
        renderCart();
    }

    function removeFromCart(id) {
        delete cart[id];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItemsList');
        const emptyMsg = document.getElementById('emptyCartMessage');
        const countBadge = document.getElementById('cartCountBadge');
        const totalElem = document.getElementById('cartGrandTotal');
        const checkoutBtn = document.getElementById('checkoutBtn');

        const keys = Object.keys(cart);
        if (keys.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted small" id="emptyCartMessage">
                    <i class="bi bi-cart-x fs-2 d-block mb-1 text-secondary"></i>
                    Keranjang belanja Anda masih kosong.<br>Klik tombol <strong>Beli</strong> pada produk pilihan Anda.
                </div>
            `;
            countBadge.textContent = '0 item';
            totalElem.textContent = 'Rp 0';
            checkoutBtn.disabled = true;
            return;
        }

        let html = '';
        let grandTotal = 0;
        let totalItems = 0;

        keys.forEach((id, index) => {
            const item = cart[id];
            const subtotal = item.quantity * item.price;
            grandTotal += subtotal;
            totalItems += item.quantity;

            html += `
                <div class="card p-2 mb-2 border rounded-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="fw-semibold small text-dark text-truncate" style="max-width: 180px;">${item.name}</div>
                        <button type="button" class="btn btn-sm text-danger p-0 ms-1 border-0" onclick="removeFromCart(${id})" title="Hapus">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Rp ${item.price.toLocaleString('id-ID')}</span>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="updateQty(${id}, -1)">-</button>
                            <span class="small fw-bold px-1">${item.quantity}</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="updateQty(${id}, 1)">+</button>
                        </div>
                    </div>
                    <div class="text-end small fw-bold text-success mt-1">
                        Rp ${subtotal.toLocaleString('id-ID')}
                    </div>
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                </div>
            `;
        });

        container.innerHTML = html;
        countBadge.textContent = totalItems + ' item';
        totalElem.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        checkoutBtn.disabled = false;
    }
</script>
@endpush
