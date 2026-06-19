<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Data Ekspedisi</h3>
            <p class="text-muted mb-0">Kelola daftar ekspedisi pengiriman beserta ongkos kirim per kilometer.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahEkspedisi">
            <i class="bi bi-plus-lg me-1"></i> Tambah Ekspedisi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center rounded-start">No</th>
                            <th>Nama Ekspedisi</th>
                            <th>Ongkir per Km (Rp)</th>
                            <th width="20%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ekspedisi as $index => $e)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $e->nama_ekspedisi }}</td>
                            <td class="fw-semibold text-primary">Rp {{ number_format($e->ongkir_per_km, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $e->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('ekspedisi.destroy', $e->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus ekspedisi {{ $e->nama_ekspedisi }}?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $e->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Ekspedisi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('ekspedisi.update', $e->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Nama Ekspedisi</label>
                                                <input type="text" name="nama_ekspedisi" class="form-control form-control-lg" value="{{ $e->nama_ekspedisi }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Ongkos Kirim per Km (Rp)</label>
                                                <input type="number" name="ongkir_per_km" class="form-control form-control-lg" value="{{ intval($e->ongkir_per_km) }}" required min="0">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-truck fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data ekspedisi.<br>Silakan tambah ekspedisi baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahEkspedisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Ekspedisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ekspedisi.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Nama Ekspedisi (Contoh: JNE, TIKI, SiCepat)</label>
                            <input type="text" name="nama_ekspedisi" class="form-control form-control-lg" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Ongkos Kirim per Km (Rp)</label>
                            <input type="number" name="ongkir_per_km" class="form-control form-control-lg" required min="0" placeholder="Contoh: 2000">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Ekspedisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
