<x-app-layout>

    <div class="d-flex align-items-start justify-content-between mb-4 gap-2 flex-wrap">
        <div>
            <div class="page-title">Aktivitas Saya</div>
            <div class="page-sub">Kelola laporan barang dan pantau status pengajuan klaim Anda</div>
        </div>
        <a href="{{ route('reports.create') }}" class="btn btn-navy px-4 py-2">
            + Buat Laporan
        </a>
    </div>

    {{-- Sub-tab Switcher (Laporan Saya | Klaim Saya) --}}
    @php $activeTab = $tab ?? request('tab', 'laporan'); @endphp
    <div class="findit-card mb-4" style="padding:6px;display:inline-flex;gap:6px;border-radius:12px;">
        <a href="{{ route('activity.index', ['tab' => 'laporan']) }}"
           style="padding:9px 20px;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;
                  {{ $activeTab === 'laporan' ? 'background:var(--navy);color:#fff;' : 'color:var(--text-2);' }}">
            📋 Laporan Saya ({{ $reports->total() }})
        </a>
        <a href="{{ route('activity.index', ['tab' => 'klaim']) }}"
           style="padding:9px 20px;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;
                  {{ $activeTab === 'klaim' ? 'background:var(--navy);color:#fff;' : 'color:var(--text-2);' }}">
            🔖 Klaim Saya ({{ $claims->total() }})
        </a>
    </div>

    @if($activeTab === 'laporan')
        {{-- Filter Laporan Saya --}}
        <div class="findit-card mb-4" style="padding:10px 14px;">
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span style="font-size:11px;color:var(--text-3);font-weight:600;">Filter:</span>
                <a href="{{ route('activity.index', ['tab' => 'laporan']) }}"
                   style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;text-decoration:none;
                          {{ !request('status') && !request('jenis') ? 'background:var(--navy);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                    Semua
                </a>
                <a href="{{ route('activity.index', ['tab' => 'laporan', 'jenis' => 'hilang']) }}"
                   style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;text-decoration:none;
                          {{ request('jenis') === 'hilang' ? 'background:var(--danger);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                    Hilang
                </a>
                <a href="{{ route('activity.index', ['tab' => 'laporan', 'jenis' => 'temuan']) }}"
                   style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;text-decoration:none;
                          {{ request('jenis') === 'temuan' ? 'background:var(--success);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                    Temuan
                </a>
            </div>
        </div>

        @if($reports->count() > 0)
            <div class="table-card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jenis</th>
                            <th>Cabang &amp; Lokasi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>
                                    <div style="font-weight:600;color:var(--text);font-size:12px;">
                                        {{ $report->nama_barang }}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-3);">
                                        {{ $report->category->nama_category ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    @if($report->jenis_laporan === 'hilang')
                                        <span class="findit-badge b-red">Hilang</span>
                                    @else
                                        <span class="findit-badge b-green">Temuan</span>
                                    @endif
                                </td>
                                <td style="font-size:11px;color:var(--text-2);">
                                    @if($report->campus)
                                        <div style="font-weight:600;color:var(--navy);">{{ $report->campus->nama_kampus }}</div>
                                    @endif
                                    <div>📍 {{ $report->lokasi }}</div>
                                </td>
                                <td style="font-size:11px;color:var(--text-2);">
                                    {{ $report->tanggal_kejadian->format('d M Y') }}
                                </td>
                                <td>
                                    @if($report->status === 'pending')
                                        <span class="findit-badge b-amber">Pending</span>
                                    @elseif($report->status === 'approved')
                                        <span class="findit-badge b-navy">Aktif</span>
                                    @elseif($report->status === 'rejected')
                                        <span class="findit-badge b-red">Ditolak</span>
                                    @else
                                        <span class="findit-badge b-green">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('reports.show', $report->id) }}"
                                           style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                                            Lihat
                                        </a>
                                        @if(in_array($report->status, ['pending', 'approved']))
                                            <span style="color:var(--border);">|</span>
                                            <a href="{{ route('reports.edit', $report->id) }}"
                                               style="font-size:11px;color:var(--warning);font-weight:600;text-decoration:none;">
                                                Edit
                                            </a>
                                        @endif
                                        {{-- Laporan yang sudah tayang menyimpan riwayat klaim, jadi hanya pending/ditolak yang bisa dihapus. --}}
                                        @if(in_array($report->status, ['pending', 'rejected']))
                                            <span style="color:var(--border);">|</span>
                                            <form method="POST"
                                                  action="{{ route('reports.destroy', $report->id) }}"
                                                  onsubmit="return confirm('Hapus laporan ini?')"
                                                  class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        style="background:none;border:none;padding:0;font-size:11px;color:var(--danger);font-weight:600;cursor:pointer;">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $reports->links() }}
            </div>
        @else
            <div class="findit-card p-5 text-center">
                <div style="font-size:32px;margin-bottom:12px;">📋</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                    Belum ada laporan
                </div>
                <div style="font-size:12px;color:var(--text-2);margin-bottom:16px;">
                    Anda belum pernah membuat laporan barang hilang atau temuan.
                </div>
                <a href="{{ route('reports.create') }}" class="btn btn-navy px-4">
                    Buat Laporan Pertama
                </a>
            </div>
        @endif
    @else
        {{-- Sub-tab Klaim Saya --}}
        @if($claims->count() > 0)
            <div class="d-flex flex-column gap-3">
                @foreach($claims as $claim)
                    <div class="findit-card p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex gap-3 align-items-start flex-grow-1">
                                <div style="width:48px;height:48px;background:var(--navy-pale);border-radius:var(--r);
                                            border:0.5px solid var(--border);display:flex;align-items:center;
                                            justify-content:center;flex-shrink:0;overflow:hidden;">
                                    @if($claim->report && $claim->report->foto_barang)
                                        <img src="{{ $claim->report->fotoUrl() }}"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:var(--navy-mid);fill:none;stroke-width:1.8;">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:var(--text);">
                                        {{ $claim->report->nama_barang ?? 'Barang' }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-2);margin-top:2px;">
                                        {{ $claim->report->category->nama_category ?? '-' }}
                                        @if($claim->report?->campus)
                                            · 🏢 {{ $claim->report->campus->nama_kampus }}
                                        @endif
                                        · 📍 {{ $claim->report->lokasi ?? '-' }}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-3);margin-top:6px;
                                                background:var(--bg);border-radius:6px;padding:6px 10px;
                                                border:0.5px solid var(--border);">
                                        <span style="font-weight:600;color:var(--text-2);">Pesan klaim:</span>
                                        {{ Str::limit($claim->pesan_klaim, 120) }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-end" style="flex-shrink:0;">
                                @if($claim->status_klaim === 'pending')
                                    <span class="findit-badge b-amber">Pending</span>
                                @elseif($claim->status_klaim === 'approved')
                                    <span class="findit-badge b-green">Disetujui</span>
                                @else
                                    <span class="findit-badge b-red">Ditolak</span>
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('reports.show', $claim->id_report) }}"
                                       style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                                        Lihat Barang →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $claims->links() }}
            </div>
        @else
            <div class="findit-card p-5 text-center">
                <div style="font-size:32px;margin-bottom:12px;">🔖</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                    Belum ada klaim
                </div>
                <div style="font-size:12px;color:var(--text-2);margin-bottom:16px;">
                    Anda belum pernah mengajukan klaim barang temuan.
                </div>
                <a href="{{ route('reports.search', ['jenis' => 'temuan']) }}" class="btn btn-navy px-4">
                    Cari Barang Temuan
                </a>
            </div>
        @endif
    @endif

</x-app-layout>
