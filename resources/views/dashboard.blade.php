<x-app-layout>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4 d-flex align-items-center text-white position-relative overflow-hidden">
                    <i class="bi bi-shop position-absolute opacity-25" style="font-size: 10rem; right: -20px; top: -30px;"></i>
                    <div class="position-relative z-index-1">
                        <h3 class="fw-bold mb-1">Selamat datang kembali, {{ Auth::user()->name }}!</h3>
                        <p class="mb-0 opacity-75">Kelola penjualan, servis, dan produk Nusantara Jaya Komputer dalam satu sistem terintegrasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir')
    <div class="row g-4 mb-4">
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Total Produk</h6>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-seam fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">124</h2>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> Tersedia di gudang</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Penjualan Hari Ini</h6>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-check fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">12</h2>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> Transaksi sukses</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Servis Berjalan</h6>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-tools fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">5</h2>
                    <small class="text-muted fw-semibold">Sedang dikerjakan</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Pendapatan Bulan Ini</h6>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">Rp 4.5M</h3>
                    <small class="text-muted fw-semibold">Estimasi kotor</small>
                </div>
            </div>
        </div>

    </div>
    @endif
</x-app-layout>