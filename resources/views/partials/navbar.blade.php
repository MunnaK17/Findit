<nav style="background:var(--navy);padding:0 18px;height:62px;
            display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;z-index:200;">

    {{-- Brand --}}
    <a href="{{ route('home') }}"
       style="font-size:20px;font-weight:800;color:#fff;text-decoration:none;
              letter-spacing:-0.5px;flex-shrink:0;">
        Find<span style="color:var(--accent);">.It</span>
    </a>

    {{-- Desktop Nav --}}
    <div class="d-none d-md-flex align-items-center gap-1">
        <a href="{{ route('home') }}"
           style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;
                  color:{{ request()->routeIs('home') ? '#fff' : 'rgba(255,255,255,0.65)' }};
                  text-decoration:none;
                  background:{{ request()->routeIs('home') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">
            Beranda
        </a>
        <a href="{{ route('reports.hilang') }}"
           style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;
                  color:{{ request()->routeIs('reports.hilang') ? '#fff' : 'rgba(255,255,255,0.65)' }};
                  text-decoration:none;
                  background:{{ request()->routeIs('reports.hilang') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">
            Barang Hilang
        </a>
        <a href="{{ route('reports.temuan') }}"
           style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;
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
               style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;
                      color:rgba(255,255,255,0.65);text-decoration:none;">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               style="padding:8px 15px;border-radius:8px;font-size:13px;font-weight:700;
                      background:var(--accent);color:var(--navy);text-decoration:none;">
                Daftar
            </a>
        @endguest

        @auth
            {{-- Notification Bell --}}
            <div id="notif-bell-wrap" style="position:relative;">
                <button id="notif-bell-btn"
                        style="position:relative;background:transparent;border:none;cursor:pointer;
                               padding:9px;border-radius:9px;color:rgba(255,255,255,0.7);
                               display:flex;align-items:center;justify-content:center;"
                        title="Notifikasi">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @if(($sharedUnreadCount ?? 0) > 0)
                        <span id="notif-badge"
                              style="position:absolute;top:4px;right:4px;
                                     background:var(--accent);color:var(--navy);
                                     font-size:11px;font-weight:800;
                                     width:20px;height:20px;border-radius:50%;
                                     display:flex;align-items:center;justify-content:center;">
                            {{ $sharedUnreadCount > 9 ? '9+' : $sharedUnreadCount }}
                        </span>
                    @endif
                </button>

                {{-- Dropdown --}}
                <div id="notif-dropdown"
                     style="display:none;position:absolute;right:0;top:calc(100% + 8px);
                            width:340px;background:#fff;border-radius:12px;
                            box-shadow:0 8px 32px rgba(0,0,0,0.15);
                            overflow:hidden;z-index:999;">
                    <div style="padding:14px 16px;border-bottom:1px solid #e5e7eb;
                                display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:16px;font-weight:700;color:#111827;">Notifikasi</span>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <button id="notif-mark-all"
                                    style="font-size:13px;color:#2563eb;
                                                                   background:transparent;border:none;cursor:pointer;">
                                Tandai semua baca
                            </button>
                            <button id="notif-close-btn"
                                    style="background:transparent;border:none;cursor:pointer;
                                                                   color:#9ca3af;padding:2px 4px;font-size:16px;line-height:1;">
                                ✕
                            </button>
                        </div>
                    </div>
                    <div id="notif-list" style="max-height:320px;overflow-y:auto;">
                        <div id="notif-empty"
                             style="padding:24px;text-align:center;color:#9ca3af;font-size:15px;">
                            Tidak ada notifikasi
                        </div>
                    </div>
                    <div style="padding:13px 16px;text-align:center;border-top:1px solid #e5e7eb;">
                        <a href="{{ route('notifications.index') }}"
                           style="font-size:14px;color:#2563eb;font-weight:600;text-decoration:none;">
                            Lihat semua notifikasi →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Laporan button (desktop only) --}}
            <a href="{{ route('reports.create') }}"
               class="d-none d-md-inline-flex"
               style="padding:8px 15px;border-radius:8px;font-size:13px;font-weight:700;
                      background:var(--accent);color:var(--navy);text-decoration:none;">
                + Laporan
            </a>

            {{-- Avatar Dropdown --}}
            <div class="dropdown">
                <div data-bs-toggle="dropdown"
                     style="width:38px;height:38px;border-radius:9px;background:var(--accent);
                            color:var(--navy);font-size:13px;font-weight:800;
                            display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow mt-2"
                    style="font-size:13px;border-radius:var(--rlg);
                           border:0.5px solid var(--border)!important;min-width:220px;">
                    <li class="px-3 pt-2 pb-1">
                        <div style="font-weight:700;color:var(--text);font-size:13px;">
                            {{ Auth::user()->name }}
                        </div>
                        <div style="font-size:11px;color:var(--text-3);">{{ Auth::user()->email }}</div>
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

{{-- Notification Bell Vanilla JS --}}
@auth
<script>
(function() {
    const bellBtn    = document.getElementById('notif-bell-btn');
    const dropdown   = document.getElementById('notif-dropdown');
    const closeBtn   = document.getElementById('notif-close-btn');
    const markAllBtn = document.getElementById('notif-mark-all');
    const notifList  = document.getElementById('notif-list');
    const notifEmpty = document.getElementById('notif-empty');
    const badge      = document.getElementById('notif-badge');

    if (!bellBtn || !dropdown) return;

    // Toggle dropdown
    bellBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = dropdown.style.display === 'block';
        dropdown.style.display = isOpen ? 'none' : 'block';
        if (!isOpen) loadNotifications();
    });

    // Close button (X)
    closeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.style.display = 'none';
    });

    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Mark all read
    markAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => {
            if (badge) badge.style.display = 'none';
            markAllBtn.style.display = 'none';
        }).catch(() => {});
    });

    // Load notifications
    function loadNotifications() {
        fetch('/notifications/api')
            .then(r => r.json())
            .then(data => {
                notifList.innerHTML = '';
                if (data.notifications.length === 0) {
                    notifList.innerHTML = '<div style="padding:24px;text-align:center;color:#9ca3af;font-size:15px;">Tidak ada notifikasi</div>';
                    return;
                }
                data.notifications.forEach(n => {
                    const bg = n.read_at ? '#fff' : '#eff6ff';
                    const div = document.createElement('div');
                    div.style.cssText = `background:${bg};padding:12px 16px;border-bottom:1px solid #f3f4f6;cursor:pointer;`;
                    div.innerHTML = `
                        <div style="font-size:15px;font-weight:600;color:#111827;display:flex;align-items:center;gap:6px;">
                            <span style="font-size:18px;">🔔</span>
                            <span>${n.title}</span>
                        </div>
                        <div style="font-size:14px;color:#6b7280;margin-top:5px;">${n.body}</div>
                        <div style="font-size:13px;color:#9ca3af;margin-top:3px;">${timeAgo(n.created_at)}</div>
                    `;
                    div.addEventListener('click', function() {
                        const url = n.data?.url || '/notifications';
                        if (!n.read_at) {
                            fetch('/notifications/' + n.id + '/read', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            }).catch(() => {});
                        }
                        window.location.href = url;
                    });
                    notifList.appendChild(div);
                });
            }).catch(() => {});
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        const diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
        if (diff < 60) return 'Baru saja';
        if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
        return Math.floor(diff / 86400) + ' hari lalu';
    }
})();
</script>
@endauth