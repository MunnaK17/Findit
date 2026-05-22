<x-admin-layout>
@section('title', 'Kelola Laporan')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Kelola Laporan</div>
            <div class="page-sub">Verifikasi dan kelola semua laporan barang</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="findit-card mb-4" style="padding:14px 16px;">
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <div class="d-flex gap-2 flex-wrap">
                <div class="search-bar-wrap" style="flex:1;min-width:200px;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama barang...">
                </div>
                <select name="jenis" class="form-select" style="width:auto;">
                    <option value="">Semua Jenis</option>
                    <option value="hilang" {{ request('jenis') === 'hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="temuan" {{ request('jenis') === 'temuan' ? 'selected' : '' }}>Temuan</option>
                </select>
                <select name="status" class="form-select" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
                    <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="submit" class="btn btn-navy px-3">Filter</button>
                @if(request('search') || request('jenis') || request('status'))
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-findit px-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Barang</th>
                    <th>Pelapor</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td style="color:var(--text-3);">{{ $report->id }}</td>
                        <td>
                            <div style="font-weight:600;font-size:12px;">{{ $report->nama_barang }}</div>
                            <div style="font-size:10px;color:var(--text-3);">{{ $report->category->nama_category }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:500;">{{ $report->user->name }}</div>
                            <div style="font-size:10px;color:var(--text-3);">{{ $report->user->nim }}</div>
                        </td>
                        <td>
                            @if($report->jenis_laporan === 'hilang')
                                <span class="findit-badge b-red">Hilang</span>
                            @else
                                <span class="findit-badge b-green">Temuan</span>
                            @endif
                        </td>
                        <td style="font-size:11px;color:var(--text-2);">{{ Str::limit($report->lokasi, 25) }}</td>
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
                            <a href="{{ route('admin.reports.show', $report->id) }}"
                               class="btn btn-outline-findit btn-sm px-3">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--text-3);padding:32px;">
                            Tidak ada laporan ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $reports->withQueryString()->links() }}
    </div>

</x-admin-layout>