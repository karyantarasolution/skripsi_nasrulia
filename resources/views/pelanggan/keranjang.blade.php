<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Keranjang Belanja</h3>
            <p class="text-muted mb-0">Periksa kembali pesanan Anda sebelum checkout.</p>
        </div>
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Lanjut Belanja
        </a>
    </div>

    @php $total = 0; $subtotal = 0; @endphp
    @forelse((array) session('keranjang') as $id => $details)
        @php $subtotal += $details['harga'] * $details['jumlah'] @endphp
    @empty
    @endforelse

    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cart me-2"></i> Daftar Produk</h6>
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse((array) session('keranjang') as $id => $details)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ Storage::url($details['foto']) }}" width="50" class="rounded me-3" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2230%22>?</text></svg>'">
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
                            <tr><td colspan="5" class="text-center py-5">
                                <i class="bi bi-cart-x fs-1 text-muted d-block mb-2"></i>
                                Keranjang belanja kosong.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i> Metode Pengiriman</h6>
                <form action="{{ route('checkout') }}" method="POST" id="formCheckout">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pengambilan</label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="metode_diambil" value="diambil" checked onchange="togglePengiriman()">
                                <label class="form-check-label fw-semibold" for="metode_diambil">
                                    <i class="bi bi-shop me-1"></i> Ambil di Toko
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="metode_diantar" value="diantar" onchange="togglePengiriman()">
                                <label class="form-check-label fw-semibold" for="metode_diantar">
                                    <i class="bi bi-truck me-1"></i> Dikirim
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="formPengiriman" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Ekspedisi</label>
                            <select name="ekspedisi_id" class="form-select form-select-lg" id="ekspedisiSelect" onchange="hitungOngkir()">
                                <option value="">-- Pilih Ekspedisi --</option>
                                @foreach($ekspedisi as $e)
                                    <option value="{{ $e->id }}" data-ongkir="{{ $e->ongkir_per_km }}">{{ $e->nama_ekspedisi }} (Rp {{ number_format($e->ongkir_per_km, 0, ',', '.') }}/km)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jarak (km)</label>
                            <input type="number" name="jarak_km" id="jarakKm" class="form-control form-control-lg" placeholder="Contoh: 5" min="0" step="0.1" oninput="hitungOngkir()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Pengiriman</label>
                            <textarea name="alamat_pengiriman" class="form-control" rows="3" placeholder="Masukkan alamat lengkap..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-3 px-5" {{ $subtotal == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check2-circle me-2"></i> CHECKOUT SEKARANG
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-primary text-white position-sticky" style="top: 80px;">
                <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i> Ringkasan Pesanan</h5>
                <hr class="opacity-25">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal ({{ count((array) session('keranjang')) }} produk):</span>
                    <span class="fw-bold" id="subtotalText">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" id="ongkirRow" style="display: none !important;">
                    <span>Ongkos Kirim:</span>
                    <span class="fw-bold" id="ongkirText">Rp 0</span>
                </div>
                <hr class="opacity-25">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold">Total Bayar:</h5>
                    <h4 class="fw-bold" id="totalBayarText">Rp {{ number_format($subtotal, 0, ',', '.') }}</h4>
                </div>
                <div class="alert alert-light text-dark mt-3 mb-0 rounded-3">
                    <small><i class="bi bi-info-circle me-1"></i> Pesanan akan diverifikasi oleh admin. Kami akan mengirimkan notifikasi melalui WhatsApp.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePengiriman() {
        var metode = document.querySelector('input[name="metode_pengambilan"]:checked').value;
        var formPengiriman = document.getElementById('formPengiriman');
        var ongkirRow = document.getElementById('ongkirRow');
        if (metode === 'diantar') {
            formPengiriman.style.display = 'block';
            ongkirRow.style.display = 'flex !important';
        } else {
            formPengiriman.style.display = 'none';
            ongkirRow.style.display = 'none !important';
            document.getElementById('ongkirText').innerText = 'Rp 0';
            hitungTotal();
        }
    }

    function hitungOngkir() {
        var subtotal = {{ $subtotal }};
        var select = document.getElementById('ekspedisiSelect');
        var jarak = parseFloat(document.getElementById('jarakKm').value) || 0;
        var ongkir = 0;
        if (select.value) {
            var ongkirPerKm = parseFloat(select.options[select.selectedIndex].getAttribute('data-ongkir')) || 0;
            ongkir = ongkirPerKm * jarak;
        }
        document.getElementById('ongkirText').innerText = 'Rp ' + ongkir.toLocaleString('id-ID');
        document.getElementById('totalBayarText').innerText = 'Rp ' + (subtotal + ongkir).toLocaleString('id-ID');
    }

    function hitungTotal() {
        var subtotal = {{ $subtotal }};
        document.getElementById('totalBayarText').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    }
    </script>
</x-app-layout>