<x-admin-layout title="Dashboard">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Dashboard Admin</div>
            <div class="page-sub">Selamat datang, {{ Auth::user()->name }}!</div>
        </div>
        <div style="font-size:11px;color:var(--text-3);">
            {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card navy">
                <div class="stat-icon" style="background:rgba(255,255,255,0.1);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div class="stat-val">{{ $totalLaporan }}</div>
                <div class="stat-lbl">Total Laporan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--accent-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--warning);fill:none;stroke-width:2;stroke-linecap:round;">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="stat-val">{{ $totalPending }}</div>
                <div class="stat-lbl">Menunggu Verifikasi</div>
                @if($totalPending > 0)
                    <div style="font-size:10px;font-weight:600;color:var(--warning);margin-top:4px;">
                        Perlu ditindaklanjuti
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--success-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--success);fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M9 11l3 3L22 4"/>
                    </svg>
                </div>
                <div class="stat-val">{{ $totalCompleted }}</div>
                <div class="stat-lbl">Berhasil Dikembalikan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--info-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--info);fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="stat-val">{{ $totalUser }}</div>
                <div class="stat-lbl">Total Mahasiswa</div>
            </div>
        </div>
    </div>

    {{-- Second Row Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--navy-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--navy);fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <div class="stat-val">{{ $totalKlaim }}</div>
                <div class="stat-lbl">Total Klaim</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--accent-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--warning);fill:none;stroke-width:2;stroke-linecap:round;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="stat-val">{{ $totalKlaimPending }}</div>
                <div class="stat-lbl">Klaim Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--danger-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--danger);fill:none;stroke-width:2;stroke-linecap:round;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <div class="stat-val">{{ \App\Models\Report::where('jenis_laporan','hilang')->where('status','approved')->count() }}</div>
                <div class="stat-lbl">Barang Hilang Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--success-light);">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--success);fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
                <div class="stat-val">{{ \App\Models\Report::where('jenis_laporan','temuan')->where('status','approved')->count() }}</div>
                <div class="stat-lbl">Barang Temuan Aktif</div>
            </div>
        </div>
    </div>

    {{-- Tables Row --}}
    <div class="row g-3">

        {{-- Laporan Terbaru --}}
        <div class="col-md-7">
            <div class="table-card">
                <div style="padding:12px 16px;border-bottom:0.5px solid var(--border);
                            display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:13px;font-weight:700;color:var(--text);">
                        Laporan Terbaru
                    </div>
                    <a href="{{ route('admin.reports.index') }}"
                       style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                        Lihat semua →
                    </a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanTerbaru as $report)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:12px;">{{ $report->nama_barang }}</div>
                                    <div style="font-size:10px;color:var(--text-3);">{{ $report->user->name }}</div>
                                </td>
                                <td>
                                    @if($report->jenis_laporan === 'hilang')
                                        <span class="findit-badge b-red">Hilang</span>
                                    @else
                                        <span class="findit-badge b-green">Temuan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->status === 'pending')
                                        <span class="findit-badge b-amber">Pending</span>
                                    @elseif($report->status === 'approved')
                                        <span class="findit-badge b-navy">Approved</span>
                                    @elseif($report->status === 'rejected')
                                        <span class="findit-badge b-red">Rejected</span>
                                    @else
                                        <span class="findit-badge b-green">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.reports.show', $report->id) }}"
                                       style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--text-3);padding:24px;">
                                    Belum ada laporan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Klaim Terbaru --}}
        <div class="col-md-5">
            <div class="table-card">
                <div style="padding:12px 16px;border-bottom:0.5px solid var(--border);
                            display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:13px;font-weight:700;color:var(--text);">
                        Klaim Terbaru
                    </div>
                    <a href="{{ route('admin.claims.index') }}"
                       style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                        Lihat semua →
                    </a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pengklaim</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($klaimTerbaru as $klaim)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:12px;">{{ $klaim->user->name }}</div>
                                    <div style="font-size:10px;color:var(--text-3);">
                                        {{ Str::limit($klaim->report->nama_barang, 20) }}
                                    </div>
                                </td>
                                <td>
                                    @if($klaim->status_klaim === 'pending')
                                        <span class="findit-badge b-amber">Pending</span>
                                    @elseif($klaim->status_klaim === 'approved')
                                        <span class="findit-badge b-green">Approved</span>
                                    @else
                                        <span class="findit-badge b-red">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.claims.show', $klaim->id) }}"
                                       style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;color:var(--text-3);padding:24px;">
                                    Belum ada klaim
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-admin-layout>