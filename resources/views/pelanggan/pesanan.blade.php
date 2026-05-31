<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Pesanan Saya</h3>
            <p class="text-muted mb-0">Pantau status pesanan dan detail transaksi Anda di sini.</p>
        </div>
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-shop me-1"></i> Belanja Lagi
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="18%">Kode Transaksi</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Total Belanja</th>
                        <th width="22%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    <tr>
                        <td class="fw-bold text-primary">{{ $p->kode_transaksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}</td>
                        <td class="fw-semibold">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($p->status == 'Lunas')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Lunas & Diproses</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Menunggu Konfirmasi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id }}">
                                <i class="bi bi-eye me-1"></i> Detail
                            </button>
                            @if($p->status == 'Lunas')
                            <a href="{{ route('pesanan.invoice', $p->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="bi bi-file-pdf"></i> Invoice
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-bag-x fs-1 text-muted opacity-50 d-block mb-3"></i>
                            <h6 class="text-muted fw-normal">Belum ada riwayat pesanan.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($pesanan as $p)
    <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Detail Pesanan: <span class="text-primary">{{ $p->kode_transaksi }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Harga Satuan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($p->detail as $d)
                                <tr class="border-bottom">
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $d->produk->nama_produk ?? 'Produk Dihapus' }}</div>
                                    </td>
                                    <td class="text-center text-muted">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold">{{ $d->jumlah }}</td>
                                    <td class="text-end fw-semibold text-primary">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3">TOTAL KESELURUHAN:</td>
                                    <td class="text-end fw-bold text-primary fs-5 py-3">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    @if($p->status != 'Lunas')
                    <div class="alert alert-warning border-0 mt-4 mb-0 d-flex align-items-center rounded-3">
                        <i class="bi bi-info-circle-fill fs-4 me-3 text-warning"></i>
                        <small class="text-dark">Silakan lakukan pembayaran langsung di toko atau hubungi admin untuk konfirmasi pesanan ini.</small>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <a href="{{ route('pesanan.invoice', $p->id) }}" target="_blank" class="btn btn-danger rounded-pill px-4 me-2">
                        <i class="bi bi-file-pdf me-1"></i> Cetak Invoice PDF
                    </a>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>
