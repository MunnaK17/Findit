<x-admin-layout>
@section('title', 'Kelola Klaim')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Kelola Klaim</div>
            <div class="page-sub">Verifikasi pengajuan klaim barang temuan</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="findit-card mb-4" style="padding:10px 14px;">
        <form method="GET" action="{{ route('admin.claims.index') }}">
            <div class="d-flex gap-2 align-items-center">
                <span style="font-size:11px;color:var(--text-3);">Filter:</span>
                <select name="status" class="form-select" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="btn btn-navy px-3">Filter</button>
                @if(request('status'))
                    <a href="{{ route('admin.claims.index') }}" class="btn btn-outline-findit px-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pengklaim</th>
                        <th>Barang</th>
                        <th>Tanggal Klaim</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                        <tr>
                            <td style="color:var(--text-3);">{{ $claim->id }}</td>
                            <td>
                                <div style="font-weight:600;font-size:12px;">{{ $claim->user->name }}</div>
                                <div style="font-size:10px;color:var(--text-3);">{{ $claim->user->nim ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size:12px;font-weight:500;">{{ $claim->report->nama_barang }}</div>
                                <div style="font-size:10px;color:var(--text-3);">{{ $claim->report->category->nama_category }}</div>
                            </td>
                            <td style="font-size:11px;color:var(--text-2);">
                                {{ \Carbon\Carbon::parse($claim->tanggal_klaim)->format('d M Y') }}
                            </td>
                            <td>
                                @if($claim->status_klaim === 'pending')
                                    <span class="findit-badge b-amber">Pending</span>
                                @elseif($claim->status_klaim === 'approved')
                                    <span class="findit-badge b-green">Approved</span>
                                @else
                                    <span class="findit-badge b-red">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 align-items-center">
                                    <a href="{{ route('admin.claims.show', $claim->id) }}"
                                       class="btn btn-outline-findit btn-sm px-3">
                                        Detail
                                    </a>
                                    @if($claim->status_klaim === 'pending')
                                        <form method="POST" action="{{ route('admin.claims.approve', $claim->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    style="background:var(--success-light);color:var(--success);border:none;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;cursor:pointer;"
                                                    onclick="return confirm('Setujui klaim ini?')">
                                                ✅
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.claims.reject', $claim->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    style="background:var(--danger-light);color:var(--danger);border:none;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;cursor:pointer;">
                                                ❌
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--text-3);padding:32px;">
                                Tidak ada klaim ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $claims->withQueryString()->links() }}
    </div>

</x-admin-layout>