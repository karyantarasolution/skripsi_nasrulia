<x-app-layout>
    <h3 class="fw-bold mb-4">Keranjang Belanja</h3>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0 @endphp
                        @forelse((array) session('keranjang') as $id => $details)
                            @php $total += $details['harga'] * $details['jumlah'] @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ Storage::url($details['foto']) }}" width="50" class="rounded me-3">
                                        <span class="fw-semibold">{{ $details['nama'] }}</span>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($details['harga'], 0, ',', '.') }}</td>
                                <td>{{ $details['jumlah'] }}</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($details['harga'] * $details['jumlah'], 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('keranjang.hapus', $id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">Keranjang kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-primary text-white">
                <h5 class="fw-bold mb-4">Ringkasan Pesanan</h5>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Bayar:</span>
                    <h4 class="fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</h4>
                </div>
                <hr>
                <form action="{{ route('checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light w-100 py-3 fw-bold rounded-3 shadow" {{ $total == 0 ? 'disabled' : '' }}>
                        CHECKOUT SEKARANG
                    </button>
                </form>
                <p class="small text-center mt-3 mb-0 opacity-75">Pesanan Anda akan langsung masuk ke sistem admin.</p>
            </div>
        </div>
    </div>
</x-app-layout>