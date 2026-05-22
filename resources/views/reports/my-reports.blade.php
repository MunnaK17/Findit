<x-app-layout>

    <div class="d-flex align-items-start justify-content-between mb-4 gap-2">
        <div>
            <div class="page-title">Laporan Saya</div>
            <div class="page-sub">Semua laporan barang yang kamu buat</div>
        </div>
        <a href="{{ route('reports.create') }}" class="btn btn-navy px-3 py-2" style="flex-shrink:0;">
            + Buat Laporan
        </a>
    </div>

    {{-- Filter --}}
    <div class="findit-card mb-4" style="padding:10px 14px;">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span style="font-size:11px;color:var(--text-3);font-weight:600;">Filter:</span>
            <a href="{{ route('my.reports') }}"
               style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;
                      text-decoration:none;white-space:nowrap;
                      {{ !request('status') && !request('jenis') ? 'background:var(--navy);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                Semua
            </a>
            <a href="{{ route('my.reports', ['jenis' => 'hilang']) }}"
               style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;
                      text-decoration:none;white-space:nowrap;
                      {{ request('jenis') === 'hilang' ? 'background:var(--danger);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                Hilang
            </a>
            <a href="{{ route('my.reports', ['jenis' => 'temuan']) }}"
               style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;
                      text-decoration:none;white-space:nowrap;
                      {{ request('jenis') === 'temuan' ? 'background:var(--success);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                Temuan
            </a>
            <a href="{{ route('my.reports', ['status' => 'pending']) }}"
               style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;
                      text-decoration:none;white-space:nowrap;
                      {{ request('status') === 'pending' ? 'background:var(--warning);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                Pending
            </a>
            <a href="{{ route('my.reports', ['status' => 'approved']) }}"
               style="padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;
                      text-decoration:none;white-space:nowrap;
                      {{ request('status') === 'approved' ? 'background:var(--navy);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:0.5px solid var(--border);' }}">
                Approved
            </a>
        </div>
    </div>

    @if($reports->count() > 0)

        {{-- DESKTOP: Tabel --}}
        <div class="table-card d-none d-md-block">
            <table class="table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
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
                                    {{ $report->category->nama_category }}
                                </div>
                            </td>
                            <td>
                                @if($report->jenis_laporan === 'hilang')
                                    <span class="findit-badge b-red">Hilang</span>
                                @else
                                    <span class="findit-badge b-green">Temuan</span>
                                @endif
                            </td>
                            <td style="font-size:11px;color:var(--text-2);">{{ $report->lokasi }}</td>
                            <td style="font-size:11px;color:var(--text-2);">
                                {{ $report->tanggal_kejadian->format('d M Y') }}
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
                                <div class="d-flex gap-2">
                                    <a href="{{ route('reports.show', $report->id) }}"
                                       style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                                        Lihat
                                    </a>
                                    @if($report->status === 'pending')
                                        <span style="color:var(--border);">|</span>
                                        <a href="{{ route('reports.edit', $report->id) }}"
                                           style="font-size:11px;color:var(--warning);font-weight:600;text-decoration:none;">
                                            Edit
                                        </a>
                                        <span style="color:var(--border);">|</span>
                                        <form method="POST"
                                              action="{{ route('reports.destroy', $report->id) }}"
                                              onsubmit="return confirm('Hapus laporan ini?')"
                                              class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    style="background:none;border:none;padding:0;
                                                           font-size:11px;color:var(--danger);
                                                           font-weight:600;cursor:pointer;">
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

        {{-- MOBILE: Card list --}}
        <div class="d-flex d-md-none flex-column gap-3">
            @foreach($reports as $report)
                <div class="findit-card p-3">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--text);">
                                {{ $report->nama_barang }}
                            </div>
                            <div style="font-size:10px;color:var(--text-3);margin-top:2px;">
                                {{ $report->category->nama_category }}
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            @if($report->jenis_laporan === 'hilang')
                                <span class="findit-badge b-red">Hilang</span>
                            @else
                                <span class="findit-badge b-green">Temuan</span>
                            @endif
                        </div>
                    </div>

                    <div style="font-size:11px;color:var(--text-2);margin-bottom:4px;">
                        📍 {{ $report->lokasi }}
                    </div>
                    <div style="font-size:11px;color:var(--text-2);margin-bottom:10px;">
                        📅 {{ $report->tanggal_kejadian->format('d M Y') }}
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        {{-- Status --}}
                        @if($report->status === 'pending')
                            <span class="findit-badge b-amber">Pending</span>
                        @elseif($report->status === 'approved')
                            <span class="findit-badge b-navy">Approved</span>
                        @elseif($report->status === 'rejected')
                            <span class="findit-badge b-red">Rejected</span>
                        @else
                            <span class="findit-badge b-green">Selesai</span>
                        @endif

                        {{-- Aksi --}}
                        <div class="d-flex gap-3">
                            <a href="{{ route('reports.show', $report->id) }}"
                               style="font-size:12px;color:var(--navy);font-weight:600;text-decoration:none;">
                                Lihat
                            </a>
                            @if($report->status === 'pending')
                                <a href="{{ route('reports.edit', $report->id) }}"
                                   style="font-size:12px;color:var(--warning);font-weight:600;text-decoration:none;">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('reports.destroy', $report->id) }}"
                                      onsubmit="return confirm('Hapus laporan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="background:none;border:none;padding:0;
                                                   font-size:12px;color:var(--danger);
                                                   font-weight:600;cursor:pointer;">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $reports->withQueryString()->links() }}
        </div>

    @else
        <div class="findit-card p-5 text-center">
            <div style="font-size:32px;margin-bottom:12px;">📋</div>
            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                Belum ada laporan
            </div>
            <div style="font-size:12px;color:var(--text-2);margin-bottom:16px;">
                Kamu belum membuat laporan apapun
            </div>
            <a href="{{ route('reports.create') }}" class="btn btn-navy px-4">
                Buat Laporan Pertama
            </a>
        </div>
    @endif

</x-app-layout>