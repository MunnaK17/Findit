<x-admin-layout>
@section('title', 'Detail Laporan')

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.reports.index') }}"
           style="color:var(--text-3);text-decoration:none;font-size:12px;">
            ← Kembali
        </a>
        <div class="page-title" style="font-size:16px;">Detail Laporan #{{ $report->id }}</div>
    </div>

    <div class="row g-3">
        <div class="col-md-8">

            {{-- Foto --}}
            @if($report->foto_barang)
                <div class="findit-card mb-3" style="overflow:hidden;">
                    <img src="{{ asset('storage/'.$report->foto_barang) }}"
                         style="width:100%;max-height:280px;object-fit:cover;">
                </div>
            @endif

            {{-- Detail --}}
            <div class="findit-card p-4 mb-3">
                <div class="d-flex gap-2 mb-3">
                    @if($report->jenis_laporan === 'hilang')
                        <span class="findit-badge b-red">Hilang</span>
                    @else
                        <span class="findit-badge b-green">Temuan</span>
                    @endif
                    <span class="findit-badge b-gray">{{ $report->category->nama_category }}</span>
                </div>

                <div style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:10px;">
                    {{ $report->nama_barang }}
                </div>

                <div style="font-size:12px;color:var(--text-2);line-height:1.7;margin-bottom:16px;">
                    {{ $report->deskripsi }}
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);">Lokasi</div>
                        <div style="font-size:12px;font-weight:600;">📍 {{ $report->lokasi }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);">Tanggal Kejadian</div>
                        <div style="font-size:12px;font-weight:600;">📅 {{ $report->tanggal_kejadian->format('d M Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);">Pelapor</div>
                        <div style="font-size:12px;font-weight:600;">👤 {{ $report->user->name }}</div>
                        <div style="font-size:10px;color:var(--text-3);">NIM: {{ $report->user->nim ?? '-' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);">Dilaporkan</div>
                        <div style="font-size:12px;font-weight:600;">🕐 {{ $report->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Daftar Klaim --}}
            @if($report->jenis_laporan === 'temuan' && $report->claims->count() > 0)
                <div class="findit-card p-4">
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;">
                        Pengajuan Klaim ({{ $report->claims->count() }})
                    </div>
                    @foreach($report->claims as $claim)
                        <div style="padding:12px;background:var(--bg);border-radius:var(--r);
                                    border:0.5px solid var(--border);margin-bottom:10px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div style="font-size:12px;font-weight:600;">{{ $claim->user->name }}</div>
                                    <div style="font-size:10px;color:var(--text-3);">
                                        NIM: {{ $claim->user->nim ?? '-' }} ·
                                        {{ \Carbon\Carbon::parse($claim->tanggal_klaim)->format('d M Y') }}
                                    </div>
                                </div>
                                @if($claim->status_klaim === 'pending')
                                    <span class="findit-badge b-amber">Pending</span>
                                @elseif($claim->status_klaim === 'approved')
                                    <span class="findit-badge b-green">Approved</span>
                                @else
                                    <span class="findit-badge b-red">Rejected</span>
                                @endif
                            </div>
                            <div style="font-size:11px;color:var(--text-2);margin-top:8px;line-height:1.6;">
                                {{ $claim->pesan_klaim }}
                            </div>
                            @if($claim->status_klaim === 'pending')
                                <div class="d-flex gap-2 mt-2">
                                    <form method="POST" action="{{ route('admin.claims.approve', $claim->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:var(--success-light);color:var(--success);border:none;font-size:11px;font-weight:700;border-radius:var(--r);"
                                                onclick="return confirm('Setujui klaim ini? Laporan akan otomatis selesai.')">
                                            ✅ Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.claims.reject', $claim->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:var(--danger-light);color:var(--danger);border:none;font-size:11px;font-weight:700;border-radius:var(--r);">
                                            ❌ Reject
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- Sidebar Aksi --}}
        <div class="col-md-4">
            <div class="findit-card p-4 mb-3">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:14px;">
                    Status & Aksi
                </div>

                {{-- Status saat ini --}}
                <div class="mb-3">
                    <div style="font-size:10px;color:var(--text-3);margin-bottom:4px;">Status saat ini</div>
                    @if($report->status === 'pending')
                        <span class="findit-badge b-amber">Pending</span>
                    @elseif($report->status === 'approved')
                        <span class="findit-badge b-navy">Approved</span>
                    @elseif($report->status === 'rejected')
                        <span class="findit-badge b-red">Rejected</span>
                    @else
                        <span class="findit-badge b-green">Completed</span>
                    @endif
                </div>

                {{-- Tombol Aksi --}}
                @if($report->status === 'pending')
                    <form method="POST" action="{{ route('admin.reports.approve', $report->id) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn w-100 py-2"
                                style="background:var(--success-light);color:var(--success);border:0.5px solid rgba(26,138,90,0.2);font-size:12px;font-weight:700;border-radius:var(--r);">
                            ✅ Approve Laporan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.reports.reject', $report->id) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn w-100 py-2"
                                style="background:var(--danger-light);color:var(--danger);border:0.5px solid rgba(220,38,38,0.2);font-size:12px;font-weight:700;border-radius:var(--r);">
                            ❌ Reject Laporan
                        </button>
                    </form>
                @elseif($report->status === 'approved')
                    <form method="POST" action="{{ route('admin.reports.reject', $report->id) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn w-100 py-2"
                                style="background:var(--danger-light);color:var(--danger);border:0.5px solid rgba(220,38,38,0.2);font-size:12px;font-weight:700;border-radius:var(--r);">
                            ❌ Reject Laporan
                        </button>
                    </form>
                @endif

                <hr style="border-color:var(--border);">

                <form method="POST" action="{{ route('admin.reports.destroy', $report->id) }}"
                      onsubmit="return confirm('Hapus laporan ini permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn w-100 py-2"
                            style="background:transparent;color:var(--danger);border:0.5px solid var(--border);font-size:12px;font-weight:700;border-radius:var(--r);">
                        🗑️ Hapus Laporan
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-admin-layout>