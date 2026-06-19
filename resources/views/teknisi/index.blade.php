<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Kelola Servis - Teknisi</h3>
            <p class="text-muted mb-0">Pantau dan update perkembangan servis yang sedang dikerjakan.</p>
        </div>
        <a href="{{ route('teknisi.semua-servis') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-list-ul me-1"></i> Semua Servis
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-4 border-bottom pb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold" id="pills-tersedia-tab" data-bs-toggle="pill" data-bs-target="#pills-tersedia" type="button" role="tab" aria-selected="true">
                <i class="bi bi-inbox me-1"></i> Tersedia
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold" id="pills-saya-tab" data-bs-toggle="pill" data-bs-target="#pills-saya" type="button" role="tab" aria-selected="false">
                <i class="bi bi-tools me-1"></i> Servis Saya
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-tersedia" role="tabpanel" tabindex="0">
            <div class="row g-3">
                @forelse($servis->whereNull('teknisi_id') as $s)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $s->jasaServis->nama_jasa ?? '-' }}</h6>
                                    <small class="text-muted">{{ $s->transaksi->kode_transaksi ?? '-' }}</small>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">{{ $s->status }}</span>
                            </div>
                            <p class="text-muted small mb-1"><i class="bi bi-person me-1"></i> {{ $s->transaksi->nama_pelanggan ?? '-' }}</p>
                            <p class="text-muted small mb-3"><i class="bi bi-chat-dots me-1"></i> {{ Str::limit($s->keluhan, 80) }}</p>
                            <form action="{{ route('teknisi.ambil', $s->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold">
                                    <i class="bi bi-hand-index me-1"></i> Ambil Servis
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-smile fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Semua servis sudah diambil teknisi.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="pills-saya" role="tabpanel" tabindex="0">
            <div class="row g-3">
                @forelse($servis->where('teknisi_id', Auth::id()) as $s)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 {{ $s->status == 'selesai' ? 'border-start border-success border-4' : ($s->status == 'proses' ? 'border-start border-primary border-4' : '') }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $s->jasaServis->nama_jasa ?? '-' }}</h6>
                                    <small class="text-muted">{{ $s->transaksi->kode_transaksi ?? '-' }}</small>
                                </div>
                                @if($s->status == 'proses')
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">Proses</span>
                                @elseif($s->status == 'selesai')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Selesai</span>
                                @elseif($s->status == 'diambil')
                                    <span class="badge bg-info px-3 py-2 rounded-pill">Diambil</span>
                                @elseif($s->status == 'garansi')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Garansi</span>
                                @elseif($s->status == 'batal')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">Batal</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-1"><i class="bi bi-person me-1"></i> {{ $s->transaksi->nama_pelanggan ?? '-' }}</p>
                            <p class="text-muted small mb-1"><i class="bi bi-chat-dots me-1"></i> {{ Str::limit($s->keluhan, 80) }}</p>
                            @if($s->catatan_teknisi)
                            <div class="bg-light rounded-3 p-2 mb-3">
                                <small class="text-muted fw-semibold">Catatan:</small>
                                <small class="text-dark d-block">{{ Str::limit($s->catatan_teknisi, 100) }}</small>
                            </div>
                            @endif
                            <button type="button" class="btn btn-outline-primary w-100 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $s->id }}">
                                <i class="bi bi-arrow-up-circle me-1"></i> Update Status
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalUpdate{{ $s->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow rounded-4">
                            <div class="modal-header border-bottom-0 pb-0">
                                <h5 class="modal-title fw-bold">Update Status Servis</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('teknisi.update-status', $s->id) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-muted small">Status Terkini</label>
                                        <select name="status" class="form-select form-select-lg" required>
                                            <option value="proses" {{ $s->status == 'proses' ? 'selected' : '' }}><i class="bi bi-gear"></i> Proses</option>
                                            <option value="selesai" {{ $s->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="diambil" {{ $s->status == 'diambil' ? 'selected' : '' }}>Diambil</option>
                                            <option value="garansi" {{ $s->status == 'garansi' ? 'selected' : '' }}>Garansi</option>
                                            <option value="batal" {{ $s->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-muted small">Catatan Teknisi</label>
                                        <textarea name="catatan_teknisi" class="form-control" rows="4" placeholder="Deskripsi perkembangan servis...">{{ $s->catatan_teknisi }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-tools fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Belum ada servis yang Anda ambil.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
