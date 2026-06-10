<div class="findit-sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">Find<span>.It</span></div>

    <div class="sidebar-section-label">Menu Utama</div>

    <a href="{{ route('admin.dashboard') }}"
       class="sitem {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        <span class="sitem-label">Dashboard</span>
    </a>

    <a href="{{ route('admin.reports.index') }}"
       class="sitem {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
        </svg>
        <span class="sitem-label">Kelola Laporan</span>
    </a>

    <a href="{{ route('admin.claims.index') }}"
       class="sitem {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
        <span class="sitem-label">Kelola Klaim</span>
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="sitem {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <span class="sitem-label">Kelola User</span>
    </a>

    <a href="{{ route('admin.categories.index') }}"
       class="sitem {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
            <line x1="7" y1="7" x2="7.01" y2="7"/>
        </svg>
        <span class="sitem-label">Kategori</span>
    </a>

    <a href="{{ route('admin.testimonials.index') }}"
       class="sitem {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span class="sitem-label">Testimoni</span>
    </a>

    {{-- Spacer --}}
    <div class="mt-auto">
        <div style="font-size:10px;color:var(--text-3);padding:8px 10px;">
            {{ Auth::user()->name }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sitem w-100 text-start"
                    style="background:none;border:none;color:var(--danger);font-size:12px;">
                Keluar
            </button>
        </form>
    </div>

</div>