<x-guest-layout>

    <div style="font-size:25px;font-weight:800;color:var(--text);margin-bottom:5px;">
        Masuk ke Akun
    </div>
    <div style="font-size:14px;color:var(--text-2);margin-bottom:30px;">
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

        {{-- Captcha --}}
        <div class="mb-4">
            <label class="form-label">Verifikasi</label>
            <div style="background:#f0f4f8;padding:12px 15px;border-radius:8px;margin-bottom:10px;display:flex;align-items:center;gap:10px;">
                <span style="font-size:18px;font-weight:700;color:var(--navy);">
                    {{ $captcha_question ?? session('captcha_question') ?? 'Refresh halaman untuk muat captcha' }}
                </span>
            </div>
            <input type="number"
                   name="captcha_answer"
                   class="form-control @error('captcha_answer') is-invalid @enderror"
                   placeholder="Ketik jawaban di sini"
                   min="0"
                   max="18"
                   style="max-width:200px;"
                   autofocus>
            @error('captcha_answer')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-navy w-100 py-2" style="font-size:16px;">
            Masuk
        </button>

        <div style="text-align:center;margin-top:20px;font-size:14px;color:var(--text-2);">
            Belum punya akun?
            <a href="{{ route('register') }}"
               style="color:var(--navy);font-weight:700;text-decoration:none;">
                Daftar di sini
            </a>
        </div>

    </form>

</x-guest-layout>