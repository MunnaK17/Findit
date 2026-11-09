<x-guest-layout>

    <div style="font-size:25px;font-weight:800;color:var(--text);margin-bottom:5px;">
        Verifikasi Email
    </div>
    <div style="font-size:14px;color:var(--text-2);margin-bottom:24px;">
        Kami mengirim kode verifikasi ke<br>
        <strong>{{ $email }}</strong><br>
        Masukkan kode tersebut untuk mengaktifkan akun Anda.
    </div>

    <form method="POST" action="{{ route('verification.otp.verify') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode Verifikasi</label>
            <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code"
                   maxlength="8" autofocus
                   class="form-control text-center @error('code') is-invalid @enderror"
                   style="font-size:26px;font-weight:800;letter-spacing:8px;"
                   placeholder="--------">
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-navy w-100 py-2" style="font-size:16px;">
            Verifikasi &amp; Masuk
        </button>
    </form>

    <form method="POST" action="{{ route('verification.otp.resend') }}" style="text-align:center;margin-top:16px;">
        @csrf
        <button type="submit" class="btn btn-link" style="font-size:14px;text-decoration:none;">
            Kirim ulang kode
        </button>
    </form>

    <div style="text-align:center;margin-top:8px;font-size:13px;color:var(--text-2);">
        Periksa juga folder spam.
        <a href="{{ route('login') }}" style="color:var(--navy);font-weight:700;text-decoration:none;">Kembali ke login</a>
    </div>

</x-guest-layout>
