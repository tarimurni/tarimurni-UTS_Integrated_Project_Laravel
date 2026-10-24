@extends('layouts.app')

@section('title', 'Tambah Produk Baru - ElectroMart')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Tambah Produk Elektronik Baru</h5>
                <p class="text-muted small mb-0">Lengkapi data inventaris, harga beli/jual, dan batas minimum stok</p>
            </div>

            <div class="card-body p-4 pt-0">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="sku" class="form-label fw-semibold small text-secondary">SKU Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" required placeholder="Contoh: ELC-ACC-005">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label fw-semibold small text-secondary">Nama Produk Elektronik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Sony WH-1000XM5 Wireless Headphones">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold small text-secondary">Kategori Produk <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label fw-semibold small text-secondary">Brand / Merk</label>
                            <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand') }}" placeholder="Contoh: Sony, ASUS, Samsung, Apple, Sharp">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="image_url" class="form-label fw-semibold small text-secondary">URL Foto Produk Asli (HD)</label>
                            <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/...">
                            <div class="form-text small">Tautan foto online Unsplash atau CDN resmi.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="color_name" class="form-label fw-semibold small text-secondary">Nama Warna Asli</label>
                            <input type="text" class="form-control @error('color_name') is-invalid @enderror" id="color_name" name="color_name" value="{{ old('color_name') }}" placeholder="Contoh: Titan Black, Space Black">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="color_hex" class="form-label fw-semibold small text-secondary">HEX Warna</label>
                            <input type="color" class="form-control form-control-color w-100 @error('color_hex') is-invalid @enderror" id="color_hex" name="color_hex" value="{{ old('color_hex', '#1C1C1E') }}" title="Pilih Warna Produk">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label fw-semibold small text-secondary">Harga Beli (Modal) Rp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required placeholder="0">
                            </div>
                            <div class="form-text small">Biaya perolehan produk dari distributor.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selling_price" class="form-label fw-semibold small text-secondary">Harga Jual Rp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" required placeholder="0">
                            </div>
                            <div class="form-text small">Harus &ge; harga beli (menjamin margin keuntungan).</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="stock" class="form-label fw-semibold small text-secondary">Stok Awal di Gudang</label>
                            <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" placeholder="0">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="minimum_stock" class="form-label fw-semibold small text-secondary">Batas Minimum Stok (Peringatan Alert) <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" required placeholder="5">
                            <div class="form-text small">Jika stok &le; angka ini, produk akan muncul di alert dashboard.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
