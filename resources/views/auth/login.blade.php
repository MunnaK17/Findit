<x-guest-layout>

    <div style="font-size:20px;font-weight:800;color:var(--text);margin-bottom:4px;">
        Masuk ke Akun
    </div>
    <div style="font-size:11px;color:var(--text-2);margin-bottom:24px;">
        Gunakan akun kampus kamu untuk melanjutkan
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="email@kampus.ac.id"
                   autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-navy w-100 py-2" style="font-size:13px;">
            Masuk
        </button>

        <div style="text-align:center;margin-top:16px;font-size:11px;color:var(--text-2);">
            Belum punya akun?
            <a href="{{ route('register') }}"
               style="color:var(--navy);font-weight:700;text-decoration:none;">
                Daftar di sini
            </a>
        </div>

    </form>

</x-guest-layout>