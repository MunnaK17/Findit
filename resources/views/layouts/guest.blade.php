<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FindIt') }} — @yield('title', 'Login')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body style="margin:0;padding:0;">

    <div class="container-fluid p-0">
        <div class="row g-0" style="min-height:100vh;">

            {{-- LEFT — Navy Branding --}}
            <div class="col-md-6 d-none d-md-flex login-left">
                <div>
                    <div class="login-brand">Find<span>.It</span></div>
                    <div class="login-tagline">
                        Sistem informasi Lost & Found kampus.<br>
                        Temukan atau laporkan barang dengan mudah.
                    </div>

                    <div class="info-pill">
                        <svg style="width:16px;height:16px;stroke:var(--accent);fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        Cari barang hilang & temuan di kampus
                    </div>
                    <div class="info-pill">
                        <svg style="width:16px;height:16px;stroke:var(--accent);fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Buat laporan barang hilang atau temuan
                    </div>
                    <div class="info-pill">
                        <svg style="width:16px;height:16px;stroke:var(--accent);fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Ajukan klaim barang milikmu
                    </div>
                </div>
            </div>

            {{-- RIGHT — Form --}}
            <div class="col-md-6 login-right">
                <div style="max-width: 360px; width: 100%; margin: 0 auto;">

                    {{-- Mobile Brand --}}
                    <div class="d-md-none text-center mb-4">
                        <div class="fw-bold fs-4" style="color:var(--navy);">Find<span style="color:var(--accent);">.It</span></div>
                        <div style="font-size:14px;color:var(--text-2);">Campus Lost & Found System</div>
                    </div>

                    {{-- Flash --}}
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    {{ $slot }}

                </div>
            </div>

        </div>
    </div>

</body>
</html>