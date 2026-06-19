<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo-square.svg') }}" alt="Logo NJK" class="img-fluid rounded-circle shadow-sm mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <h4 class="fw-bold text-primary mb-1">Daftar Akun Baru</h4>
                        <p class="text-muted small">Nusantara Jaya Komputer</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                            <input id="name" class="form-control form-control-lg @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama Anda">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Alamat Email</label>
                            <input id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_whatsapp" class="form-label fw-semibold">No. WhatsApp <span class="text-muted">(opsional)</span></label>
                            <input id="no_whatsapp" class="form-control form-control-lg @error('no_whatsapp') is-invalid @enderror" type="text" name="no_whatsapp" value="{{ old('no_whatsapp') }}" placeholder="08xxxxxxxxxx">
                            <small class="text-muted">Untuk notifikasi pesanan via WhatsApp</small>
                            @error('no_whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" type="password" name="password" required placeholder="Min. 8 karakter">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                            <input id="password_confirmation" class="form-control form-control-lg" type="password" name="password_confirmation" required placeholder="Ulangi password">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Daftar</button>
                        </div>

                        <div class="text-center mt-4">
                            <span class="text-muted small">Sudah punya akun?</span>
                            <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold">Masuk disini</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
