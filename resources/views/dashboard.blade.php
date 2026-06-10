<x-app-layout>

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Dashboard</div>
            <div class="page-sub">Selamat datang, {{ Auth::user()->name }}!</div>
        </div>
        <a href="{{ route('reports.create') }}" class="btn btn-navy px-4">
            + Buat Laporan
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card navy">
                <div class="stat-icon" style="background:rgba(255,255,255,0.1);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div class="stat-val">{{ Auth::user()->reports()->count() }}</div>
                <div class="stat-lbl">Total Laporan Saya</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--accent-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--warning);fill:none;stroke-width:2;stroke-linecap:round;">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="stat-val">{{ Auth::user()->reports()->where('status','pending')->count() }}</div>
                <div class="stat-lbl">Laporan Pending</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--success-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--success);fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <div class="stat-val">{{ Auth::user()->claims()->count() }}</div>
                <div class="stat-lbl">Klaim Saya</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="findit-card p-4">
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">
                    Laporan Terbaru Saya
                </div>
                <div style="font-size:12px;color:var(--text-2);margin-bottom:17px;">
                    5 laporan terakhir yang kamu buat
                </div>

                @forelse(Auth::user()->reports()->with('category')->latest()->take(5)->get() as $report)
                    <div style="display:flex;align-items:center;justify-content:space-between;
                                padding:11px 0;border-bottom:0.5px solid var(--border);">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text);">
                                {{ $report->nama_barang }}
                            </div>
                            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">
                                {{ $report->category->nama_category }} ·
 {{ $report->tanggal_kejadian->format('d M Y') }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($report->jenis_laporan === 'hilang')
                                <span class="findit-badge b-red">Hilang</span>
                            @else
                                <span class="findit-badge b-green">Temuan</span>
                            @endif
                            @if($report->status === 'pending')
                                <span class="findit-badge b-amber">Pending</span>
                            @elseif($report->status === 'approved')
                                <span class="findit-badge b-navy">Approved</span>
                            @elseif($report->status === 'rejected')
                                <span class="findit-badge b-red">Rejected</span>
                            @else
                                <span class="findit-badge b-green">Selesai</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:26px 0;color:var(--text-3);font-size:13px;">
                        Belum ada laporan.
                        <a href="{{ route('reports.create') }}" style="color:var(--navy);font-weight:700;">
                            Buat sekarang
                        </a>
                    </div>
                @endforelse

                @if(Auth::user()->reports()->count() > 0)
                    <a href="{{ route('my.reports') }}"
                       style="display:block;text-align:center;margin-top:13px;
                              font-size:12px;color:var(--navy);font-weight:700;text-decoration:none;">
                        Lihat semua laporan →
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="findit-card p-4">
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">
                    Menu Cepat
                </div>
                <div style="font-size:12px;color:var(--text-2);margin-bottom:17px;">
                    Akses fitur utama FindIt
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('reports.create') }}"
                       style="display:flex;align-items:center;gap:13px;padding:13px;
                              background:var(--navy-pale);border-radius:var(--r);
                              text-decoration:none;color:var(--text);transition:all .15s;">
                        <div style="width:34px;height:34px;background:var(--navy);border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2.5;">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">Buat Laporan</div>
                            <div style="font-size:11px;color:var(--text-2);">Laporkan barang hilang atau temuan</div>
                        </div>
                    </a>

                    <a href="{{ route('reports.temuan') }}"
                       style="display:flex;align-items:center;gap:13px;padding:13px;
                              background:var(--navy-pale);border-radius:var(--r);
                              text-decoration:none;color:var(--text);transition:all .15s;">
                        <div style="width:34px;height:34px;background:var(--success);border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">Cari Barang Temuan</div>
                            <div style="font-size:11px;color:var(--text-2);">Lihat daftar barang yang ditemukan</div>
                        </div>
                    </a>

                    <a href="{{ route('my.claims') }}"
                       style="display:flex;align-items:center;gap:13px;padding:13px;
                              background:var(--navy-pale);border-radius:var(--r);
                              text-decoration:none;color:var(--text);transition:all .15s;">
                        <div style="width:34px;height:34px;background:var(--accent);border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--navy);fill:none;stroke-width:2;">
                                <path d="M9 11l3 3L22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">Klaim Saya</div>
                            <div style="font-size:11px;color:var(--text-2);">Lihat status pengajuan klaim</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>