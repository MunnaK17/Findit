@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — FindIt | {{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Responsive Table Styles for Admin --}}
    <style>
    /* Table wrapper for horizontal scroll on mobile */
    .table-responsive-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
    }

    .table-responsive-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive-wrapper::-webkit-scrollbar-track {
        background: var(--bg);
        border-radius: 3px;
    }

    .table-responsive-wrapper::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 3px;
    }

    .table-responsive-wrapper::-webkit-scrollbar-thumb:hover {
        background: var(--text-3);
    }

    /* Ensure table doesn't shrink below min-width */
    .table-responsive-wrapper .table {
        min-width: 700px;
        white-space: nowrap;
    }

    /* Mobile adjustments */
    @media (max-width: 767.98px) {
        .table-responsive-wrapper .table {
            font-size: 11px;
        }

        .table-responsive-wrapper .table th,
        .table-responsive-wrapper .table td {
            padding: 8px 10px;
        }
    }
    </style>
</head>
<body style="margin:0;padding:0;">

    <div class="d-flex">

        {{-- Sidebar --}}
        @include('partials.sidebar-admin')

        {{-- Main Area --}}
        <div class="flex-grow-1" style="min-height:100vh;overflow-x:hidden;">

            {{-- Topbar — hamburger + avatar + dropdown --}}
            <div style="height:56px;background:var(--bg2);border-bottom:0.5px solid var(--border);
                        display:flex;align-items:center;justify-content:space-between;
                        padding:0 24px;position:sticky;top:0;z-index:100;">

                {{-- Left side: Hamburger (mobile) + Page title --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Hamburger Button (Mobile only) --}}
                    <button class="sidebar-hamburger d-md-none" title="Menu"
                            style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;
                                   background:none;border:none;border-radius:8px;cursor:pointer;color:var(--text);">
                        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;">
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>

                    {{-- Judul halaman --}}
                    <div style="font-size:13px;font-weight:600;color:var(--text-2);">
                        {{ $title }}
                    </div>
                </div>

                {{-- Avatar + Dropdown --}}
                <div class="dropdown">
                    <div class="nav-avatar dropdown-toggle"
                         data-bs-toggle="dropdown"
                         aria-expanded="false"
                         style="cursor:pointer;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                        style="font-size:12px;border-radius:var(--rlg);
                               border:0.5px solid var(--border)!important;min-width:200px;">
                        <li class="px-3 pt-2 pb-1">
                            <div style="font-weight:700;color:var(--text);font-size:12px;">
                                {{ Auth::user()->name }}
                            </div>
                            <div style="font-size:10px;color:var(--text-3);">Admin</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                 Admin Panel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}">
                                Dashboard Utama
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"
                                        style="color:var(--danger);">
                                    keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success') || session('error'))
                <div style="padding:16px 24px 0;">
                    @if(session('success'))
                        <div class="alert alert-success">✅ {{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">❌ {{ session('error') }}</div>
                    @endif
                </div>
            @endif

            {{-- Content --}}
            <div style="padding:24px;">
                {{ $slot }}
            </div>

        </div>
    </div>

</body>
</html>