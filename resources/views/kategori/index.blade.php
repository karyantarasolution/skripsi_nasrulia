<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Manajemen Kategori</h3>
            <p class="text-muted mb-0">Kelola daftar kategori untuk produk Anda.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
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
                            <th>Nama Kategori</th>
                            <th width="20%" class="text-center">Jumlah Produk</th>
                            <th width="20%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategori as $index => $k)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-semibold text-dark">{{ $k->nama_kategori }}</td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">
                                    {{ $k->produk_count }} Produk
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $k->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                
                                <form action="{{ route('kategori.destroy', $k->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('YAKIN HAPUS? Semua produk di dalam kategori ini juga akan terhapus permanen!')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $k->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $k->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $k->id }}">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('kategori.update', $k->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Nama Kategori</label>
                                                <input type="text" name="nama_kategori" class="form-control form-control-lg" value="{{ $k->nama_kategori }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
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
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data kategori.<br>Silakan tambah kategori baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control form-control-lg" placeholder="Contoh: Laptop, Aksesoris, Hardware..." required autofocus>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>