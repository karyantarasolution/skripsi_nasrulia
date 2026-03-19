<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Layanan Jasa Servis</h3>
            <p class="text-muted mb-0">Kelola daftar layanan servis komputer beserta biayanya.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJasa">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jasa
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
                            <th>Nama Layanan Servis</th>
                            <th>Biaya Jasa (Rp)</th>
                            <th width="20%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jasa as $index => $j)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $j->nama_jasa }}</td>
                            <td class="fw-semibold text-primary">Rp {{ number_format($j->biaya_jasa, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $j->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                
                                <form action="{{ route('jasa.destroy', $j->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus jasa {{ $j->nama_jasa }}?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $j->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Jasa Servis</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('jasa.update', $j->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Nama Layanan</label>
                                                <input type="text" name="nama_jasa" class="form-control form-control-lg" value="{{ $j->nama_jasa }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Biaya Jasa (Rp)</label>
                                                <input type="number" name="biaya_jasa" class="form-control form-control-lg" value="{{ intval($j->biaya_jasa) }}" required min="0">
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
                                <i class="bi bi-tools fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data jasa servis.<br>Silakan tambah layanan baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahJasa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Jasa Servis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jasa.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Nama Layanan (Contoh: Instal Ulang OS)</label>
                            <input type="text" name="nama_jasa" class="form-control form-control-lg" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Biaya Jasa (Rp)</label>
                            <input type="number" name="biaya_jasa" class="form-control form-control-lg" required min="0">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Layanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>