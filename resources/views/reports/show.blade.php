<x-app-layout>

    <div class="row g-4">

        {{-- LEFT — Detail Barang --}}
        <div class="col-md-8">

            {{-- Breadcrumb --}}
            <div style="font-size:11px;color:var(--text-3);margin-bottom:16px;">
                <a href="{{ route('home') }}" style="color:var(--text-3);text-decoration:none;">Home</a>
                <span class="mx-1">›</span>
                <a href="{{ $report->jenis_laporan === 'hilang' ? route('reports.hilang') : route('reports.temuan') }}"
                   style="color:var(--text-3);text-decoration:none;">
                    Barang {{ ucfirst($report->jenis_laporan) }}
                </a>
                <span class="mx-1">›</span>
                <span style="color:var(--text);">{{ $report->nama_barang }}</span>
            </div>

            {{-- Foto --}}
            <div class="findit-card mb-3" style="overflow:hidden;">
                @if($report->foto_barang)
                    <img src="{{ asset('storage/'.$report->foto_barang) }}"
                         style="width:100%;max-height:320px;object-fit:cover;">
                @else
                    <div style="height:200px;background:var(--navy-pale);display:flex;
                                align-items:center;justify-content:center;">
                        <svg viewBox="0 0 24 24" style="width:48px;height:48px;stroke:var(--navy-mid);fill:none;stroke-width:1.5;">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="findit-card p-4 mb-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    @if($report->jenis_laporan === 'hilang')
                        <span class="findit-badge b-red">Hilang</span>
                    @else
                        <span class="findit-badge b-green">Temuan</span>
                    @endif
                    @if($report->status === 'approved')
                        <span class="findit-badge b-navy">Diverifikasi</span>
                    @elseif($report->status === 'completed')
                        <span class="findit-badge b-green">Selesai</span>
                    @endif
                    <span class="findit-badge b-gray">{{ $report->category->nama_category }}</span>
                </div>

                <h5 style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:8px;">
                    {{ $report->nama_barang }}
                </h5>

                <p style="font-size:12px;color:var(--text-2);line-height:1.7;margin-bottom:0;">
                    {{ $report->deskripsi }}
                </p>
            </div>

            {{-- Detail --}}
            <div class="findit-card p-4">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:14px;">
                    Informasi Detail
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);margin-bottom:3px;">Lokasi</div>
                        <div style="font-size:12px;font-weight:600;color:var(--text);">
                            📍 {{ $report->lokasi }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);margin-bottom:3px;">Tanggal Kejadian</div>
                        <div style="font-size:12px;font-weight:600;color:var(--text);">
                            📅 {{ $report->tanggal_kejadian->format('d M Y') }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);margin-bottom:3px;">Dilaporkan oleh</div>
                        <div style="font-size:12px;font-weight:600;color:var(--text);">
                            👤 {{ $report->user->name }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px;color:var(--text-3);margin-bottom:3px;">Tanggal Laporan</div>
                        <div style="font-size:12px;font-weight:600;color:var(--text);">
                            🕐 {{ $report->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT — Aksi --}}
        <div class="col-md-4">

            {{-- Tombol Klaim --}}
            @auth
                @if($report->bisaDiklaim() && Auth::id() !== $report->id_user)
                    {{-- Cek sudah klaim atau belum --}}
                    @php
                        $sudahKlaim = \App\Models\Claim::where('id_report', $report->id)
                            ->where('id_user', Auth::id())->exists();
                    @endphp

                    @if($sudahKlaim)
                        <div class="findit-card p-4 mb-3 text-center">
                            <div style="font-size:24px;margin-bottom:8px;">✅</div>
                            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:4px;">
                                Klaim Sudah Diajukan
                            </div>
                            <div style="font-size:11px;color:var(--text-2);">
                                Tunggu konfirmasi dari admin
                            </div>
                        </div>
                    @else
                        <div class="findit-card p-4 mb-3">
                            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:4px;">
                                Ini barang milikmu?
                            </div>
                            <div style="font-size:11px;color:var(--text-2);margin-bottom:16px;">
                                Ajukan klaim dengan memberikan penjelasan mengapa barang ini milikmu.
                            </div>
                            <a href="{{ route('claims.create', $report->id) }}"
                               class="btn btn-navy w-100 py-2">
                                Ajukan Klaim
                            </a>
                        </div>
                    @endif
                @endif

                @if(Auth::id() === $report->id_user && $report->status === 'pending')
                    <div class="findit-card p-4 mb-3">
                        <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:12px;">
                            Kelola Laporan
                        </div>
                        <a href="{{ route('reports.edit', $report->id) }}"
                           class="btn btn-outline-findit w-100 mb-2 py-2">
                            ✏️ Edit Laporan
                        </a>
                        <form method="POST" action="{{ route('reports.destroy', $report->id) }}"
                              onsubmit="return confirm('Yakin hapus laporan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn w-100 py-2"
                                    style="background:var(--danger-light);color:var(--danger);border:none;font-size:12px;font-weight:700;border-radius:var(--r);">
                                🗑️ Hapus Laporan
                            </button>
                        </form>
                    </div>
                @endif
            @else
                @if($report->jenis_laporan === 'temuan' && $report->status === 'approved')
                    <div class="findit-card p-4 mb-3 text-center">
                        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:8px;">
                            Ini barang milikmu?
                        </div>
                        <div style="font-size:11px;color:var(--text-2);margin-bottom:16px;">
                            Login terlebih dahulu untuk mengajukan klaim
                        </div>
                        <a href="{{ route('login') }}" class="btn btn-navy w-100 py-2">
                            Login untuk Klaim
                        </a>
                    </div>
                @endif
            @endauth

            {{-- Status Card --}}
            <div class="findit-card p-4 mb-3">
                <div style="font-size:11px;font-weight:700;color:var(--text-3);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
                    Status Laporan
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($report->status === 'pending')
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--warning);flex-shrink:0;"></div>
                        <span style="font-size:12px;font-weight:600;color:var(--warning);">Menunggu Verifikasi</span>
                    @elseif($report->status === 'approved')
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--success);flex-shrink:0;"></div>
                        <span style="font-size:12px;font-weight:600;color:var(--success);">Diverifikasi Admin</span>
                    @elseif($report->status === 'rejected')
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--danger);flex-shrink:0;"></div>
                        <span style="font-size:12px;font-weight:600;color:var(--danger);">Ditolak</span>
                    @elseif($report->status === 'completed')
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--success);flex-shrink:0;"></div>
                        <span style="font-size:12px;font-weight:600;color:var(--success);">Selesai / Dikembalikan</span>
                    @endif
                </div>
            </div>

            {{-- Jumlah Klaim --}}
            @if($report->jenis_laporan === 'temuan')
                <div class="findit-card p-4">
                    <div style="font-size:11px;font-weight:700;color:var(--text-3);
                                text-transform:uppercase;letter-spacing:0.07em;margin-bottom:8px;">
                        Pengajuan Klaim
                    </div>
                    <div style="font-size:24px;font-weight:800;color:var(--navy);">
                        {{ $report->claims->count() }}
                    </div>
                    <div style="font-size:11px;color:var(--text-2);">klaim masuk</div>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>