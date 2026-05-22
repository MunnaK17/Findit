<x-guest-layout>

    <div style="font-size:20px;font-weight:800;color:var(--text);margin-bottom:4px;">
        Buat Akun Baru
    </div>
    <div style="font-size:11px;color:var(--text-2);margin-bottom:24px;">
        Daftar untuk mulai menggunakan FindIt
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Nama sesuai KTM"
                   autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">NIM</label>
            <input type="text" name="nim"
                   class="form-control @error('nim') is-invalid @enderror"
                   value="{{ old('nim') }}"
                   placeholder="Nomor Induk Mahasiswa">
            @error('nim')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="email@kampus.ac.id">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Min. 8 karakter">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation"
                   class="form-control"
                   placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn btn-navy w-100 py-2" style="font-size:13px;">
            Daftar Sekarang
        </button>

        <div style="text-align:center;margin-top:16px;font-size:11px;color:var(--text-2);">
            Sudah punya akun?
            <a href="{{ route('login') }}"
               style="color:var(--navy);font-weight:700;text-decoration:none;">
                Masuk di sini
            </a>
        </div>

    </form>

</x-guest-layout>