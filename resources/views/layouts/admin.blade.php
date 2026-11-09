<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — FindIt | @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])0.0
</head>
<body style="margin:0;padding:0;">

    <div class="d-flex">

        {{-- Sidebar --}}
        @include('partials.sidebar-admin')

        {{-- Main Area --}}
        <div class="flex-grow-1" style="min-height:100vh;overflow-x:hidden;">

            {{-- Topbar --}}
            <div style="height:56px;background:var(--bg2);border-bottom:0.5px solid var(--border);
                        display:flex;align-items:center;justify-content:space-between;
                        padding:0 24px;position:sticky;top:0;z-index:100;">
                <div style="font-size:13px;font-weight:600;color:var(--text-2);">
                    @yield('title', 'Dashboard')
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size:11px;color:var(--text-3);">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="nav-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>

            {{-- Flash --}}
            @if (session('success') || session('error'))
                <div style="padding: 16px 24px 0;">
                    @if (session('success'))
                        <div class="alert alert-success">✅ {{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">❌ {{ session('error') }}</div>
                    @endif
                </div>
            @endif

            {{-- Content --}}
            <div style="padding: 24px;">
                {{ $slot }}
            </div>

        </div>
    </div>

</body>
</html>