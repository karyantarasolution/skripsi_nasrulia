<x-app-layout>
    @php
        $total_produk = \App\Models\Produk::count();
        $penjualan_hari_ini = \App\Models\Transaksi::whereDate('created_at', \Carbon\Carbon::today())->count();
        $servis_berjalan = \App\Models\ServisDetail::where('status', 'proses')->count();
        $pendapatan_bulan_ini = \App\Models\Transaksi::where('status', 'Lunas')
            ->whereMonth('updated_at', \Carbon\Carbon::now()->month)
            ->whereYear('updated_at', \Carbon\Carbon::now()->year)
            ->sum('total_bayar');
    @endphp

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

    @if(Auth::user()->peran == 'teknisi')
    <div class="row g-4 mb-4">
        @php
            $servis_saya = \App\Models\ServisDetail::where('teknisi_id', Auth::id())->count();
            $servis_proses = \App\Models\ServisDetail::where('teknisi_id', Auth::id())->where('status', 'proses')->count();
            $servis_selesai = \App\Models\ServisDetail::where('teknisi_id', Auth::id())->where('status', 'selesai')->count();
            $servis_tersedia = \App\Models\ServisDetail::whereNull('teknisi_id')->count();
        @endphp
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Servis Saya</h6>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-tools fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_saya }}</h2>
                    <small class="text-muted fw-semibold">Total servis ditangani</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Sedang Diproses</h6>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-gear fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_proses }}</h2>
                    <small class="text-muted fw-semibold">Masih dikerjakan</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Selesai</h6>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_selesai }}</h2>
                    <small class="text-success fw-semibold">Sudah selesai</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Tersedia</h6>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-inbox fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_tersedia }}</h2>
                    <small class="text-muted fw-semibold">Belum diambil</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir')
    {{-- Kartu Statistik Utama --}}
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
                    <h2 class="fw-bold mb-0 text-dark">{{ $total_produk }}</h2>
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
                    <h2 class="fw-bold mb-0 text-dark">{{ $penjualan_hari_ini }}</h2>
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
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_berjalan }}</h2>
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
                    <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($pendapatan_bulan_ini, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Transaksi Lunas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Keuangan --}}
    @php
        // Total pendapatan keseluruhan (lunas)
        $total_pendapatan = \App\Models\Transaksi::lunas()->sum('total_bayar');

        // Total transaksi
        $total_transaksi_lunas = \App\Models\Transaksi::lunas()->count();
        $total_transaksi_pending = \App\Models\Transaksi::where('status', '!=', 'Lunas')->count();

        // Pendapatan penjualan vs servis
        $pendapatan_penjualan = \App\Models\Transaksi::lunas()->penjualan()->sum('total_bayar');
        $pendapatan_servis = \App\Models\Transaksi::lunas()->servis()->sum('total_bayar');

        // Laba bersih (HPP)
        $detail_penjualan = \App\Models\TransaksiDetail::whereHas('transaksi', fn($q) => $q->lunas()->penjualan())
            ->with('produk')
            ->get();
        $total_hpp = 0;
        foreach ($detail_penjualan as $d) {
            if ($d->produk) {
                $total_hpp += $d->produk->harga_beli * $d->jumlah;
            }
        }
        $laba_bersih = $pendapatan_penjualan - $total_hpp;

        // Pendapatan 6 bulan terakhir
        $bulan_labels = [];
        $pendapatan_bulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bln = \Carbon\Carbon::now()->subMonths($i);
            $bulan_labels[] = $bln->translatedFormat('M Y');
            $val = \App\Models\Transaksi::lunas()
                ->whereMonth('created_at', $bln->month)
                ->whereYear('created_at', $bln->year)
                ->sum('total_bayar');
            $pendapatan_bulanan[] = (int) $val;
        }

        // Top 5 produk terlaris
        $produk_terlaris = \App\Models\TransaksiDetail::whereHas('transaksi', fn($q) => $q->lunas())
            ->selectRaw('produk_id, SUM(jumlah) as total_terjual, SUM(subtotal) as total_pendapatan')
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $nama_produk = [];
        $jumlah_terjual = [];
        foreach ($produk_terlaris as $pt) {
            $nama_produk[] = $pt->produk ? $pt->produk->nama_produk : 'N/A';
            $jumlah_terjual[] = (int) $pt->total_terjual;
        }
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #198754 0%, #157347 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-white-50 fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Total Pendapatan</h6>
                        <i class="bi bi-cash-stack fs-4 opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                    <small class="opacity-75">Semua transaksi lunas</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-white-50 fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Laba Bersih</h6>
                        <i class="bi bi-graph-up-arrow fs-4 opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Rp {{ number_format($laba_bersih, 0, ',', '.') }}</h3>
                    <small class="opacity-75">Pendapatan - HPP</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                <div class="card-body p-4 text-dark">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-dark fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Transaksi Lunas</h6>
                        <i class="bi bi-check-circle fs-4 opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $total_transaksi_lunas }}</h3>
                    <small class="text-dark opacity-75">Transaksi selesai</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-white-50 fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Transaksi Pending</h6>
                        <i class="bi bi-clock-history fs-4 opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $total_transaksi_pending }}</h3>
                    <small class="opacity-75">Belum lunas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row g-4 mb-4">
        {{-- Grafik Pendapatan Bulanan --}}
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i> Pendapatan 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartPendapatan" height="280"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Komposisi Pendapatan --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2 text-success"></i> Komposisi Pendapatan</h6>
                </div>
                <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                    <div style="max-width: 280px; width: 100%;">
                        <canvas id="chartKomposisi" height="280"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Grafik Top Produk --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i> Top 5 Produk Terlaris</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(count($nama_produk) > 0)
                    <canvas id="chartProduk" height="250"></canvas>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p>Belum ada data penjualan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ringkasan Perbandingan --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-info"></i> Ringkasan Perbandingan</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold text-dark"><i class="bi bi-cart-check text-success me-2"></i>Penjualan</span>
                            <span class="fw-bold">Rp {{ number_format($pendapatan_penjualan, 0, ',', '.') }}</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $total_pendapatan > 0 ? ($pendapatan_penjualan / $total_pendapatan * 100) : 0 }}%; border-radius: 10px;"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold text-dark"><i class="bi bi-tools text-primary me-2"></i>Servis</span>
                            <span class="fw-bold">Rp {{ number_format($pendapatan_servis, 0, ',', '.') }}</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" style="width: {{ $total_pendapatan > 0 ? ($pendapatan_servis / $total_pendapatan * 100) : 0 }}%; border-radius: 10px;"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold text-dark"><i class="bi bi-box text-warning me-2"></i>HPP (Biaya Produk)</span>
                            <span class="fw-bold">Rp {{ number_format($total_hpp, 0, ',', '.') }}</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ $pendapatan_penjualan > 0 ? ($total_hpp / $pendapatan_penjualan * 100) : 0 }}%; border-radius: 10px;"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-5"><i class="bi bi-graph-up-arrow text-success me-2"></i>Laba Bersih</span>
                        <span class="fw-bold text-success fs-4">Rp {{ number_format($laba_bersih, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const bulanLabels = @json($bulan_labels);
        const pendapatanBulanan = @json($pendapatan_bulanan);
        const namaProduk = @json($nama_produk);
        const jumlahTerjual = @json($jumlah_terjual);
        const pendapatanPenjualan = {{ (int) $pendapatan_penjualan }};
        const pendapatanServis = {{ (int) $pendapatan_servis }};

        // Chart 1: Pendapatan Bulanan (Bar Chart)
        new Chart(document.getElementById('chartPendapatan'), {
            type: 'bar',
            data: {
                labels: bulanLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: pendapatanBulanan,
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: [
                        'rgba(13, 110, 253, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + ctx.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Chart 2: Komposisi Pendapatan (Doughnut)
        new Chart(document.getElementById('chartKomposisi'), {
            type: 'doughnut',
            data: {
                labels: ['Penjualan', 'Servis'],
                datasets: [{
                    data: [pendapatanPenjualan, pendapatanServis],
                    backgroundColor: ['#198754', '#0d6efd'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Chart 3: Top Produk (Horizontal Bar)
        if (namaProduk.length > 0) {
            new Chart(document.getElementById('chartProduk'), {
                type: 'bar',
                data: {
                    labels: namaProduk,
                    datasets: [{
                        label: 'Unit Terjual',
                        data: jumlahTerjual,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.raw + ' unit terjual';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    </script>
    @endif
</x-app-layout>
