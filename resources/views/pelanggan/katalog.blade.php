<x-app-layout>
 <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Katalog Produk NJK</h3>
        
        @if(Auth::user()->peran == 'pelanggan')
        <a href="{{ route('keranjang.index') }}" class="btn btn-dark rounded-pill px-4">
            <i class="bi bi-cart3 me-2"></i> Keranjang 
            <span class="badge bg-primary ms-1">{{ count((array) session('keranjang')) }}</span>
        </a>
        @endif
    </div>

    <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-sm btn-outline-primary rounded-pill {{ !request('kategori') ? 'active' : '' }}">Semua</a>
        @foreach($kategori as $k)
            <a href="?kategori={{ $k->id }}" class="btn btn-sm btn-outline-primary rounded-pill {{ request('kategori') == $k->id ? 'active' : '' }}">{{ $k->nama_kategori }}</a>
        @endforeach
    </div>

    <div class="row g-4">
        @forelse($produk as $p)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 product-card">
                <div class="position-relative">
                    @if($p->foto)
                        <img src="{{ Storage::url($p->foto) }}" class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 200px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    @endif
                    <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm">{{ $p->kategori->nama_kategori }}</span>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-1 text-dark">{{ $p->nama_produk }}</h6>
                    <p class="text-muted small mb-3 text-truncate">{{ $p->deskripsi ?? 'Ready stock unit premium.' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</span>
                        <span class="small text-muted">Stok: {{ $p->stok }}</span>
                    </div>
                </div>
           <div class="card-footer bg-white border-0 pb-4 px-3">
                    @if(Auth::user()->peran == 'pelanggan')
                        @if($p->stok > 0)
                        <form action="{{ route('keranjang.tambah', $p->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100 rounded-3 fw-semibold">
                                + Keranjang
                            </button>
                        </form>
                        @else
                        <button class="btn btn-secondary w-100 rounded-3" disabled>Stok Habis</button>
                        @endif
                    @else
                        <button class="btn btn-light w-100 rounded-3 text-muted" disabled>
                            Hanya Mode Tampil
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <h5 class="text-muted">Produk tidak ditemukan.</h5>
        </div>
        @endforelse
    </div>
</x-app-layout>