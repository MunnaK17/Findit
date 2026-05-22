<nav style="background:var(--navy);padding:0 16px;height:56px;
            display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;z-index:200;">

    {{-- Brand --}}
    <a href="{{ route('home') }}"
       style="font-size:18px;font-weight:800;color:#fff;text-decoration:none;
              letter-spacing:-0.5px;flex-shrink:0;">
        Find<span style="color:var(--accent);">.It</span>
    </a>

    {{-- Desktop Nav --}}
    <div class="d-none d-md-flex align-items-center gap-1">
        <a href="{{ route('home') }}"
           style="padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;
                  color:{{ request()->routeIs('home') ? '#fff' : 'rgba(255,255,255,0.65)' }};
                  text-decoration:none;
                  background:{{ request()->routeIs('home') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">
            Beranda
        </a>
        <a href="{{ route('reports.hilang') }}"
           style="padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;
                  color:{{ request()->routeIs('reports.hilang') ? '#fff' : 'rgba(255,255,255,0.65)' }};
                  text-decoration:none;
                  background:{{ request()->routeIs('reports.hilang') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">
            Barang Hilang
        </a>
        <a href="{{ route('reports.temuan') }}"
           style="padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;
                  color:{{ request()->routeIs('reports.temuan') ? '#fff' : 'rgba(255,255,255,0.65)' }};
                  text-decoration:none;
                  background:{{ request()->routeIs('reports.temuan') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">
            Barang Temuan
        </a>
    </div>

    {{-- Right Side --}}
    <div class="d-flex align-items-center gap-2">
        @guest
            <a href="{{ route('login') }}"
               style="padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;
                      color:rgba(255,255,255,0.65);text-decoration:none;">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               style="padding:7px 14px;border-radius:7px;font-size:12px;font-weight:700;
                      background:var(--accent);color:var(--navy);text-decoration:none;">
                Daftar
            </a>
        @endguest

        @auth
            {{-- Laporan button (desktop only) --}}
            <a href="{{ route('reports.create') }}"
               class="d-none d-md-inline-flex"
               style="padding:7px 14px;border-radius:7px;font-size:12px;font-weight:700;
                      background:var(--accent);color:var(--navy);text-decoration:none;">
                + Laporan
            </a>

            {{-- Avatar Dropdown --}}
            <div class="dropdown">
                <div data-bs-toggle="dropdown"
                     style="width:34px;height:34px;border-radius:8px;background:var(--accent);
                            color:var(--navy);font-size:12px;font-weight:800;
                            display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow mt-2"
                    style="font-size:12px;border-radius:var(--rlg);
                           border:0.5px solid var(--border)!important;min-width:200px;">
                    <li class="px-3 pt-2 pb-1">
                        <div style="font-weight:700;color:var(--text);font-size:12px;">
                            {{ Auth::user()->name }}
                        </div>
                        <div style="font-size:10px;color:var(--text-3);">{{ Auth::user()->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}">📊 Dashboard</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('my.reports') }}">📋 Laporan Saya</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('my.claims') }}">🔖 Klaim Saya</a>
                    </li>

                    {{-- Mobile nav links --}}
                    <li class="d-md-none"><hr class="dropdown-divider my-1"></li>
                    <li class="d-md-none">
                        <a class="dropdown-item" href="{{ route('home') }}">🏠 Beranda</a>
                    </li>
                    <li class="d-md-none">
                        <a class="dropdown-item" href="{{ route('reports.hilang') }}">🔍 Barang Hilang</a>
                    </li>
                    <li class="d-md-none">
                        <a class="dropdown-item" href="{{ route('reports.temuan') }}">📦 Barang Temuan</a>
                    </li>
                    <li class="d-md-none">
                        <a class="dropdown-item" href="{{ route('reports.create') }}">➕ Buat Laporan</a>
                    </li>

                    @if(Auth::user()->role === 'admin')
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item fw-bold"
                               style="color:var(--navy);"
                               href="{{ route('admin.dashboard') }}">⚙️ Admin Panel</a>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item" style="color:var(--danger);">
                                🚪 Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</nav>