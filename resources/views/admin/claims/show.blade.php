<x-admin-layout>
@section('title', 'Detail Klaim')

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.claims.index') }}"
           style="color:var(--text-3);text-decoration:none;font-size:12px;">← Kembali</a>
        <div class="page-title" style="font-size:16px;">Detail Klaim #{{ $claim->id }}</div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">

            {{-- Info Barang --}}
            <div class="findit-card p-4 mb-3">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
                    Barang yang Diklaim
                </div>
                <div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:4px;">
                    {{ $claim->report->nama_barang }}
                </div>
                <div class="d-flex gap-2 mb-3">
                    <span class="findit-badge b-green">Temuan</span>
                    <span class="findit-badge b-gray">{{ $claim->report->category->nama_category }}</span>
                </div>
                <div style="font-size:12px;color:var(--text-2);">📍 {{ $claim->report->lokasi }}</div>
                <div style="font-size:12px;color:var(--text-2);">
                    Dilaporkan oleh: <strong>{{ $claim->report->user->name }}</strong>
                </div>
            </div>

            {{-- Pesan Klaim --}}
            <div class="findit-card p-4">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
                    Pesan Klaim
                </div>
                <div style="font-size:12px;color:var(--text);line-height:1.8;
                            background:var(--bg);border-radius:var(--r);padding:14px;">
                    {{ $claim->pesan_klaim }}
                </div>
                <div style="font-size:10px;color:var(--text-3);margin-top:8px;">
                    Diajukan pada {{ \Carbon\Carbon::parse($claim->tanggal_klaim)->format('d M Y') }}
                </div>
            </div>

        </div>

        <div class="col-md-5">

            {{-- Info Pengklaim --}}
            <div class="findit-card p-4 mb-3">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
                    Identitas Pengklaim
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $claim->user->name }}</div>
                <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $claim->user->email }}</div>
                <div style="font-size:12px;color:var(--text-2);">NIM: {{ $claim->user->nim ?? '-' }}</div>
            </div>

            {{-- Aksi --}}
            <div class="findit-card p-4">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
                    Status & Aksi
                </div>

                <div class="mb-3">
                    @if($claim->status_klaim === 'pending')
                        <span class="findit-badge b-amber">Pending</span>
                    @elseif($claim->status_klaim === 'approved')
                        <span class="findit-badge b-green">Approved</span>
                    @else
                        <span class="findit-badge b-red">Rejected</span>
                    @endif
                </div>

                @if($claim->status_klaim === 'pending')
                    <form method="POST" action="{{ route('admin.claims.approve', $claim->id) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn w-100 py-2"
                                style="background:var(--success-light);color:var(--success);border:0.5px solid rgba(26,138,90,0.2);font-size:12px;font-weight:700;border-radius:var(--r);"
                                onclick="return confirm('Setujui klaim ini? Laporan akan otomatis selesai.')">
                            ✅ Approve Klaim
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.claims.reject', $claim->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn w-100 py-2"
                                style="background:var(--danger-light);color:var(--danger);border:0.5px solid rgba(220,38,38,0.2);font-size:12px;font-weight:700;border-radius:var(--r);">
                            ❌ Reject Klaim
                        </button>
                    </form>
                @elseif($claim->status_klaim === 'approved')
                    <div style="background:var(--success-light);border-radius:var(--r);padding:12px;font-size:12px;color:var(--success);">
                        ✅ Klaim ini sudah disetujui. Laporan otomatis selesai.
                    </div>
                @else
                    <div style="background:var(--danger-light);border-radius:var(--r);padding:12px;font-size:12px;color:var(--danger);">
                        ❌ Klaim ini sudah ditolak.
                    </div>
                @endif
            </div>

        </div>
    </div>

</x-admin-layout>