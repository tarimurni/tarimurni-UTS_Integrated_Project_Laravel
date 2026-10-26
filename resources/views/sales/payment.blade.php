@extends('layouts.app')

@section('title', 'ElectroPay Gateway - ' . $sale->sale_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-9 col-xl-8">

        <!-- Top Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Invoice (#{{ $sale->sale_number }})
            </a>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                <i class="bi bi-shield-check me-1"></i>ElectroPay Secure Gateway
            </span>
        </div>

        <!-- Snap Payment Card Container -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <!-- Header Midtrans Snap Style -->
            <div class="p-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0d9488 100%);">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="bg-primary text-white p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-lightning-charge-fill fs-5"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-white">ElectroPay <span class="badge bg-white text-dark ms-1" style="font-size: 0.65rem;">Powered by Snap</span></h5>
                                <div class="text-white-50 small">Gerbang Pembayaran Elektronik Terintegrasi</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-sm-end">
                        <div class="small text-white-50">Total Tagihan:</div>
                        <div class="fs-4 fw-bold text-warning font-monospace">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                    </div>
                </div>

                <hr class="border-secondary my-3 opacity-25">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small text-light">
                    <div>
                        <i class="bi bi-receipt me-1 text-primary"></i> <strong>Order ID:</strong> <span class="font-monospace">{{ $sale->sale_number }}</span>
                    </div>
                    <div>
                        <i class="bi bi-hash me-1 text-info"></i> <strong>Ref Gateway:</strong> <span class="font-monospace">{{ $sale->payment_reference ?? 'PG-MID-' . date('Ymd') . '-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <i class="bi bi-clock-history me-1 text-warning"></i> Sisa Waktu: <strong id="countdownTimer" class="font-monospace text-warning">14:59</strong>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 bg-light">

                <!-- Payment Status Banner -->
                @if($sale->payment_status === 'paid')
                    <div class="alert alert-success border-success-subtle d-flex align-items-center justify-content-between mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-check-lg fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Transaksi Telah LUNAS (PAID)</h6>
                                <p class="mb-0 text-muted small">Pembayaran terverifikasi otomatis via <strong>{{ $sale->payment_method_label }}</strong> pada {{ $sale->paid_at ? $sale->paid_at->format('d M Y, H:i') : '-' }} WIB.</p>
                            </div>
                        </div>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-success btn-sm px-3">
                            <i class="bi bi-receipt me-1"></i> Buka Struk
                        </a>
                    </div>
                @elseif($sale->payment_status === 'refunded')
                    <div class="alert alert-danger border-danger-subtle d-flex align-items-center gap-3 mb-4 shadow-sm">
                        <i class="bi bi-arrow-counterclockwise fs-2 text-danger"></i>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Status Pembayaran: DIBATALKAN / REFUNDED</h6>
                            <p class="mb-0 text-muted small">Transaksi ini telah dibatalkan oleh Administrator dan stok telah diretur.</p>
                        </div>
                    </div>
                @endif

                <!-- Payment Channels Navigation Tabs -->
                <ul class="nav nav-pills nav-fill bg-white p-1 rounded-3 border mb-4 shadow-sm" id="paymentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 {{ $sale->payment_method === 'qris' || empty($sale->payment_method) ? 'active' : '' }}" id="qris-tab" data-bs-toggle="pill" data-bs-target="#tab-qris" type="button" role="tab">
                            <i class="bi bi-qr-code-scan me-1 text-danger"></i> QRIS Instant
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 {{ in_array($sale->payment_method, ['va_bca', 'va_bsi', 'va_mandiri']) ? 'active' : '' }}" id="va-tab" data-bs-toggle="pill" data-bs-target="#tab-va" type="button" role="tab">
                            <i class="bi bi-bank2 me-1 text-primary"></i> Virtual Account
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 {{ $sale->payment_method === 'ewallet' ? 'active' : '' }}" id="ewallet-tab" data-bs-toggle="pill" data-bs-target="#tab-ewallet" type="button" role="tab">
                            <i class="bi bi-phone-fill me-1 text-info"></i> E-Wallet
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 {{ $sale->payment_method === 'cash' ? 'active' : '' }}" id="cash-tab" data-bs-toggle="pill" data-bs-target="#tab-cash" type="button" role="tab">
                            <i class="bi bi-cash-stack me-1 text-success"></i> Tunai Kasir
                        </button>
                    </li>
                </ul>

                <!-- Tab Panes -->
                <div class="tab-content" id="paymentTabContent">

                    <!-- TAB 1: QRIS -->
                    <div class="tab-pane fade {{ $sale->payment_method === 'qris' || empty($sale->payment_method) ? 'show active' : '' }}" id="tab-qris" role="tabpanel">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-6 text-center">
                                <!-- Realistic National QRIS Card -->
                                <div class="bg-white p-3 rounded-4 shadow-sm border mx-auto d-inline-block" style="max-width: 310px; width: 100%;">
                                    <div class="bg-danger text-white py-1 px-3 rounded-2 fw-bold small text-center mb-2 letter-spacing-1 d-flex align-items-center justify-content-between">
                                        <span style="font-size: 0.85rem;"><i class="bi bi-qr-code me-1"></i> QRIS</span>
                                        <span class="badge bg-white text-danger" style="font-size: 0.65rem;">GPN</span>
                                    </div>
                                    <div class="text-center my-1">
                                        <div class="fw-bold text-dark fs-6">ELECTROMART OFFICIAL STORE</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">NMID: ID1020261026001 &bull; A01</div>
                                    </div>

                                    <!-- QR Code Vector Graphic -->
                                    <div class="p-2 border rounded-3 bg-white my-2 d-flex justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="180" height="180">
                                            <!-- Corner Finder 1 -->
                                            <rect x="10" y="10" width="50" height="50" fill="#000" rx="4" />
                                            <rect x="18" y="18" width="34" height="34" fill="#fff" rx="2" />
                                            <rect x="25" y="25" width="20" height="20" fill="#000" rx="2" />

                                            <!-- Corner Finder 2 -->
                                            <rect x="140" y="10" width="50" height="50" fill="#000" rx="4" />
                                            <rect x="148" y="18" width="34" height="34" fill="#fff" rx="2" />
                                            <rect x="155" y="25" width="20" height="20" fill="#000" rx="2" />

                                            <!-- Corner Finder 3 -->
                                            <rect x="10" y="140" width="50" height="50" fill="#000" rx="4" />
                                            <rect x="18" y="148" width="34" height="34" fill="#fff" rx="2" />
                                            <rect x="25" y="155" width="20" height="20" fill="#000" rx="2" />

                                            <!-- Data Pixels Pattern -->
                                            <rect x="70" y="15" width="8" height="8" fill="#000" />
                                            <rect x="85" y="15" width="8" height="8" fill="#000" />
                                            <rect x="100" y="15" width="8" height="8" fill="#000" />
                                            <rect x="115" y="15" width="8" height="8" fill="#000" />
                                            <rect x="70" y="30" width="8" height="8" fill="#000" />
                                            <rect x="100" y="30" width="8" height="8" fill="#000" />
                                            <rect x="70" y="45" width="8" height="8" fill="#000" />
                                            <rect x="85" y="45" width="8" height="8" fill="#000" />
                                            <rect x="115" y="45" width="8" height="8" fill="#000" />

                                            <rect x="15" y="70" width="8" height="8" fill="#000" />
                                            <rect x="30" y="70" width="8" height="8" fill="#000" />
                                            <rect x="45" y="70" width="8" height="8" fill="#000" />
                                            <rect x="65" y="65" width="70" height="70" fill="#f8fafc" rx="8" />
                                            <circle cx="100" cy="100" r="26" fill="#0d9488" />
                                            <path d="M96 90 L108 100 L96 110" stroke="#ffffff" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                            <text x="100" y="125" text-anchor="middle" font-size="7" font-weight="bold" fill="#0f172a">ELECTROPAY</text>

                                            <rect x="145" y="70" width="8" height="8" fill="#000" />
                                            <rect x="160" y="70" width="8" height="8" fill="#000" />
                                            <rect x="175" y="70" width="8" height="8" fill="#000" />

                                            <rect x="70" y="145" width="8" height="8" fill="#000" />
                                            <rect x="85" y="145" width="8" height="8" fill="#000" />
                                            <rect x="100" y="145" width="8" height="8" fill="#000" />
                                            <rect x="115" y="145" width="8" height="8" fill="#000" />
                                            <rect x="145" y="145" width="8" height="8" fill="#000" />
                                            <rect x="160" y="145" width="8" height="8" fill="#000" />
                                            <rect x="175" y="145" width="8" height="8" fill="#000" />

                                            <rect x="70" y="160" width="8" height="8" fill="#000" />
                                            <rect x="115" y="160" width="8" height="8" fill="#000" />
                                            <rect x="145" y="160" width="8" height="8" fill="#000" />
                                            <rect x="175" y="160" width="8" height="8" fill="#000" />

                                            <rect x="70" y="175" width="8" height="8" fill="#000" />
                                            <rect x="85" y="175" width="8" height="8" fill="#000" />
                                            <rect x="100" y="175" width="8" height="8" fill="#000" />
                                            <rect x="145" y="175" width="8" height="8" fill="#000" />
                                        </svg>
                                    </div>

                                    <div class="text-center">
                                        <div class="text-muted" style="font-size: 0.65rem;">Dicetak Otomatis oleh Sistem ElectroPay</div>
                                        <div class="fw-bold text-danger fs-6">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-info-circle-fill text-danger me-1"></i> Petunjuk Pembayaran QRIS:
                                </h6>
                                <ol class="small text-muted ps-3 mb-3">
                                    <li>Buka aplikasi perbankan digital (BCA Mobile, Livin' Mandiri, BRImo, BSI Mobile) atau E-Wallet (GoPay, OVO, DANA, ShopeePay).</li>
                                    <li>Pilih menu <strong>Pindai / Scan QRIS</strong> di aplikasi Anda.</li>
                                    <li>Arahkan kamera ke kode QRIS di samping.</li>
                                    <li>Periksa nama penerima: <strong>ELECTROMART OFFICIAL STORE</strong> dengan total <strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong>.</li>
                                    <li>Masukkan PIN transaksi Anda untuk menyelesaikan pembayaran.</li>
                                </ol>

                                <div class="p-3 bg-white border rounded-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="small text-muted">Aplikasi yang Didukung:</span>
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem;">Semua Bank & Dompet Digital</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="badge bg-light text-dark border">BCA</span>
                                        <span class="badge bg-light text-dark border">Mandiri</span>
                                        <span class="badge bg-light text-dark border">BRI</span>
                                        <span class="badge bg-light text-dark border">BSI</span>
                                        <span class="badge bg-light text-dark border">GoPay</span>
                                        <span class="badge bg-light text-dark border">DANA</span>
                                        <span class="badge bg-light text-dark border">OVO</span>
                                        <span class="badge bg-light text-dark border">ShopeePay</span>
                                    </div>
                                </div>

                                <!-- Simulation Button -->
                                <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="qris">
                                    <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> Simulasikan Pembayaran QRIS Berhasil (Callback Lunas)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: VIRTUAL ACCOUNT -->
                    <div class="tab-pane fade {{ in_array($sale->payment_method, ['va_bca', 'va_bsi', 'va_mandiri']) ? 'show active' : '' }}" id="tab-va" role="tabpanel">
                        <div class="row g-4">
                            <!-- Bank Switcher Pills -->
                            <div class="col-12">
                                <div class="p-3 bg-white border rounded-3">
                                    <label class="form-label small fw-bold text-secondary mb-2">Pilih Bank Virtual Account:</label>
                                    <div class="row g-2">
                                        <!-- BCA -->
                                        <div class="col-md-4">
                                            <div class="border rounded-3 p-3 text-center cursor-pointer {{ $sale->payment_method === 'va_bca' ? 'border-primary bg-primary-subtle' : 'bg-light' }}" onclick="selectBankVA('va_bca', 'BCA', '80777 0812 6900 201')">
                                                <div class="fw-bold text-primary mb-1"><i class="bi bi-bank2 me-1"></i> Bank BCA</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">BCA Virtual Account</div>
                                            </div>
                                        </div>
                                        <!-- BSI -->
                                        <div class="col-md-4">
                                            <div class="border rounded-3 p-3 text-center cursor-pointer {{ $sale->payment_method === 'va_bsi' ? 'border-teal bg-teal-subtle' : 'bg-light' }}" onclick="selectBankVA('va_bsi', 'BSI', '90012 0812 6900 201')">
                                                <div class="fw-bold mb-1" style="color: #00a39e;"><i class="bi bi-bank me-1"></i> Bank BSI</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">BSI Syariah VA</div>
                                            </div>
                                        </div>
                                        <!-- Mandiri -->
                                        <div class="col-md-4">
                                            <div class="border rounded-3 p-3 text-center cursor-pointer {{ $sale->payment_method === 'va_mandiri' ? 'border-warning bg-warning-subtle' : 'bg-light' }}" onclick="selectBankVA('va_mandiri', 'Mandiri', '88901 0812 6900 201')">
                                                <div class="fw-bold text-warning mb-1"><i class="bi bi-wallet2 me-1"></i> Bank Mandiri</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Mandiri Bill Payment</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VA Display Box -->
                            <div class="col-12">
                                <div class="bg-white p-4 border rounded-4 shadow-sm text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-1 mb-2" id="vaBankBadge">
                                        {{ $sale->payment_method_label }}
                                    </span>
                                    <div class="text-muted small">Nomor Rekening Virtual Account:</div>
                                    <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                                        <span class="fs-3 fw-bold font-monospace text-dark" id="vaNumberDisplay">
                                            {{ $sale->virtual_account_number }}
                                        </span>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="copyVA()" title="Salin Nomor VA">
                                            <i class="bi bi-clipboard me-1"></i> Salin
                                        </button>
                                    </div>
                                    <div id="copyAlert" class="small text-success fw-semibold" style="display: none;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Nomor Virtual Account berhasil disalin ke clipboard!
                                    </div>
                                    <div class="text-muted small mt-2">
                                        Nama Penerima: <strong>ELECTROMART OFFICIAL</strong> &bull; Total: <strong class="text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- VA Instructions Accordion -->
                            <div class="col-12">
                                <div class="accordion" id="vaInstructionsAccordion">
                                    <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed py-2 fw-semibold small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMbanking">
                                                <i class="bi bi-phone me-2 text-primary"></i> Cara Pembayaran via Mobile Banking (m-BCA / BSI Mobile / Livin')
                                            </button>
                                        </h2>
                                        <div id="collapseMbanking" class="accordion-collapse collapse" data-bs-parent="#vaInstructionsAccordion">
                                            <div class="accordion-body small text-muted">
                                                1. Buka aplikasi Mobile Banking di ponsel Anda dan Login.<br>
                                                2. Pilih menu <strong>Transfer / Pembayaran</strong> &gt; <strong>Virtual Account</strong>.<br>
                                                3. Masukkan Nomor Virtual Account di atas: <strong class="text-dark">{{ $sale->virtual_account_number }}</strong>.<br>
                                                4. Periksa rincian pesanan dan total tagihan (Rp {{ number_format($sale->total, 0, ',', '.') }}).<br>
                                                5. Masukkan PIN Mobile Banking Anda untuk menyelesaikan pembayaran.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border rounded-3 overflow-hidden">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed py-2 fw-semibold small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAtm">
                                                <i class="bi bi-credit-card me-2 text-success"></i> Cara Pembayaran via Mesin ATM
                                            </button>
                                        </h2>
                                        <div id="collapseAtm" class="accordion-collapse collapse" data-bs-parent="#vaInstructionsAccordion">
                                            <div class="accordion-body small text-muted">
                                                1. Masukkan kartu ATM dan PIN Anda.<br>
                                                2. Pilih menu <strong>Transaksi Lainnya</strong> &gt; <strong>Transfer</strong> &gt; <strong>Ke Rekening Virtual Account</strong>.<br>
                                                3. Masukkan Nomor Virtual Account di atas dan tekan <strong>Benar</strong>.<br>
                                                4. Konfirmasi data transaksi dan simpan struk bukti transfer.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Simulation Button -->
                            <div class="col-12">
                                <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST" id="vaSimulateForm">
                                    @csrf
                                    <input type="hidden" name="payment_method" id="vaMethodInput" value="{{ in_array($sale->payment_method, ['va_bca', 'va_bsi', 'va_mandiri']) ? $sale->payment_method : 'va_bca' }}">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> Simulasikan Pembayaran Virtual Account Berhasil (Callback Lunas)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: E-WALLET -->
                    <div class="tab-pane fade {{ $sale->payment_method === 'ewallet' ? 'show active' : '' }}" id="tab-ewallet" role="tabpanel">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-5 text-center">
                                <div class="p-4 bg-white border rounded-4 shadow-sm">
                                    <div class="d-inline-flex p-3 bg-info-subtle text-info rounded-circle mb-3">
                                        <i class="bi bi-phone-vibrate fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Push Notification E-Wallet</h6>
                                    <p class="text-muted small mb-0">Notifikasi tagihan otomatis dikirim ke akun dompet digital Anda.</p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold text-dark mb-2">Instruksi Pembayaran E-Wallet:</h6>
                                <ol class="small text-muted ps-3 mb-3">
                                    <li>Buka notifikasi di ponsel Anda dari aplikasi <strong>GoPay, OVO, DANA</strong>, atau <strong>ShopeePay</strong>.</li>
                                    <li>Pastikan nominal tagihan sesuai dengan <strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong>.</li>
                                    <li>Konfirmasi pembayaran dengan saldo E-Wallet Anda sebelum batas waktu berakhir.</li>
                                </ol>

                                <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="ewallet">
                                    <button type="submit" class="btn btn-info text-white w-100 py-2 fw-semibold shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> Simulasikan Konfirmasi Pembayaran E-Wallet (Callback Lunas)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: TUNAI KASIR -->
                    <div class="tab-pane fade {{ $sale->payment_method === 'cash' ? 'show active' : '' }}" id="tab-cash" role="tabpanel">
                        <div class="p-4 bg-white border rounded-4 text-center">
                            <div class="d-inline-flex p-3 bg-success-subtle text-success rounded-circle mb-3">
                                <i class="bi bi-cash-stack fs-1"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Pembayaran Tunai di Toko (Kasir)</h6>
                            <p class="text-muted small mb-3">Serahkan uang tunai sebesar <strong class="text-success fs-6">Rp {{ number_format($sale->total, 0, ',', '.') }}</strong> kepada petugas kasir ElectroMart di counter.</p>

                            <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_method" value="cash">
                                <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i> Konfirmasi Pembayaran Tunai Diterima
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Footer Security Guarantee -->
            <div class="card-footer bg-white p-3 text-center border-top">
                <div class="d-flex justify-content-center align-items-center gap-3 small text-muted">
                    <span><i class="bi bi-shield-fill-check text-success me-1"></i>Enkripsi 256-Bit SSL</span>
                    <span>&bull;</span>
                    <span><i class="bi bi-patch-check-fill text-primary me-1"></i>Midtrans Snap Verified</span>
                    <span>&bull;</span>
                    <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Callback Otomatis</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Countdown Timer 15:00
    let duration = 15 * 60;
    const timerDisplay = document.getElementById('countdownTimer');
    const timerInterval = setInterval(function () {
        const minutes = Math.floor(duration / 60);
        const seconds = duration % 60;
        timerDisplay.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        if (--duration < 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = 'KEDALUWARSA';
            timerDisplay.classList.remove('text-warning');
            timerDisplay.classList.add('text-danger');
        }
    }, 1000);

    // Salin Nomor VA
    function copyVA() {
        const vaText = document.getElementById('vaNumberDisplay').innerText.replace(/\s+/g, '');
        navigator.clipboard.writeText(vaText).then(function() {
            const alertBox = document.getElementById('copyAlert');
            alertBox.style.display = 'block';
            setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
        });
    }

    // Switch Bank VA dynamically
    function selectBankVA(method, bankName, vaNumber) {
        document.getElementById('vaMethodInput').value = method;
        document.getElementById('vaBankBadge').innerText = bankName + ' Virtual Account';
        document.getElementById('vaNumberDisplay').innerText = vaNumber;
    }
</script>
@endpush
