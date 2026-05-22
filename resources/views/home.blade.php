<x-app-layout>

{{-- HERO --}}
<div class="findit-hero" style="margin: -24px -24px 0; border-radius: 0;">
    <div>
        <div class="hero-eyebrow">🎓 Universitas BSI</div>
        <h1 class="hero-title">Temukan Barangmu<br>yang <span>Hilang</span></h1>
        <p class="hero-desc">
            Platform Lost & Found kampus. Laporkan barang hilang atau temuan,
            dan bantu sesama mahasiswa mendapatkan barangnya kembali.
        </p>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.temuan') }}" class="btn btn-accent px-4 py-2">
                Cari Barang Temuan
            </a>
            @auth
                <a href="{{ route('reports.create') }}" class="btn px-4 py-2"
                   style="background:rgba(255,255,255,0.1);color:#fff;border:0.5px solid rgba(255,255,255,0.25);">
                    + Buat Laporan
                </a>
            @else
                <a href="{{ route('register') }}" class="btn px-4 py-2"
                   style="background:rgba(255,255,255,0.1);color:#fff;border:0.5px solid rgba(255,255,255,0.25);">
                    Daftar Sekarang
                </a>
            @endauth
        </div>
    </div>

    <div class="d-none d-md-flex gap-3">
        <div class="hero-stat-box">
            <div class="hero-stat-val">{{ \App\Models\Report::where('jenis_laporan','temuan')->where('status','approved')->count() }}</div>
            <div class="hero-stat-lbl">Barang Temuan</div>
        </div>
        <div class="hero-stat-box">
            <div class="hero-stat-val">{{ \App\Models\Report::where('jenis_laporan','hilang')->where('status','approved')->count() }}</div>
            <div class="hero-stat-lbl">Barang Hilang</div>
        </div>
        <div class="hero-stat-box">
            <div class="hero-stat-val">{{ \App\Models\Report::where('status','completed')->count() }}</div>
            <div class="hero-stat-lbl">Berhasil Kembali</div>
        </div>
    </div>
</div>

{{-- BARANG TEMUAN TERBARU --}}
<div class="mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="page-title">Barang Temuan Terbaru</div>
            <div class="page-sub">Barang yang baru saja ditemukan di kampus</div>
        </div>
        <a href="{{ route('reports.temuan') }}"
           style="font-size:12px;color:var(--navy);font-weight:700;text-decoration:none;">
            Lihat semua →
        </a>
    </div>

    @if($laporanTemuan->count() > 0)
        <div class="row g-3">
            @foreach($laporanTemuan as $report)
                <div class="col-md-4 col-lg-2">
                    <a href="{{ route('reports.show', $report->id) }}" class="item-card">
                        <div class="item-card-img">
                            @if($report->foto_barang)
                                <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            @endif
                        </div>
                        <div class="item-card-body">
                            <div class="item-card-name">{{ $report->nama_barang }}</div>
                            <div class="item-card-loc">📍 {{ $report->lokasi }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="findit-badge b-green">Temuan</span>
                                <span style="font-size:10px;color:var(--text-3);">
                                    {{ $report->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="findit-card p-4 text-center" style="color:var(--text-3);font-size:12px;">
            Belum ada barang temuan yang diverifikasi.
        </div>
    @endif
</div>

{{-- BARANG HILANG TERBARU --}}
<div class="mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="page-title">Barang Hilang Terbaru</div>
            <div class="page-sub">Barang yang sedang dicari pemiliknya</div>
        </div>
        <a href="{{ route('reports.hilang') }}"
           style="font-size:12px;color:var(--navy);font-weight:700;text-decoration:none;">
            Lihat semua →
        </a>
    </div>

    @if($laporanHilang->count() > 0)
        <div class="row g-3">
            @foreach($laporanHilang as $report)
                <div class="col-md-4 col-lg-2">
                    <a href="{{ route('reports.show', $report->id) }}" class="item-card">
                        <div class="item-card-img">
                            @if($report->foto_barang)
                                <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            @endif
                        </div>
                        <div class="item-card-body">
                            <div class="item-card-name">{{ $report->nama_barang }}</div>
                            <div class="item-card-loc">📍 {{ $report->lokasi }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="findit-badge b-red">Hilang</span>
                                <span style="font-size:10px;color:var(--text-3);">
                                    {{ $report->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="findit-card p-4 text-center" style="color:var(--text-3);font-size:12px;">
            Belum ada laporan barang hilang.
        </div>
    @endif
</div>

{{-- HOW IT WORKS --}}
<div class="mt-5 mb-2">
    <div class="text-center mb-4">
        <div class="page-title">Cara Kerja FindIt</div>
        <div class="page-sub">3 langkah mudah untuk menemukan barangmu kembali</div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="findit-card p-4 text-center">
                <div style="width:48px;height:48px;background:var(--navy);border-radius:12px;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;">1. Buat Laporan</div>
                <div style="font-size:11px;color:var(--text-2);line-height:1.6;">
                    Laporkan barang hilang atau temuan dengan detail lengkap dan foto.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="findit-card p-4 text-center">
                <div style="width:48px;height:48px;background:var(--accent);border-radius:12px;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:var(--navy);fill:none;stroke-width:2;stroke-linecap:round;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;">2. Admin Verifikasi</div>
                <div style="font-size:11px;color:var(--text-2);line-height:1.6;">
                    Admin memverifikasi laporan agar informasi yang tampil akurat dan terpercaya.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="findit-card p-4 text-center">
                <div style="width:48px;height:48px;background:var(--success);border-radius:12px;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;">3. Ajukan Klaim</div>
                <div style="font-size:11px;color:var(--text-2);line-height:1.6;">
                    Pemilik mengajukan klaim, admin konfirmasi, barang dikembalikan.
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>