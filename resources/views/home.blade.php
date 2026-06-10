<x-app-layout>
@php
    $countTemuan = \App\Models\Report::where('jenis_laporan', 'temuan')->where('status', 'approved')->count();
    $countHilang = \App\Models\Report::where('jenis_laporan', 'hilang')->where('status', 'approved')->count();
    $countSelesai = \App\Models\Report::where('status', 'completed')->count();

    $imageFor = fn ($report) => $report->foto_barang ? asset('storage/' . $report->foto_barang) : null;
@endphp

<div class="landing-page">
    <section class="landing-hero">
        <div class="landing-hero__glow landing-hero__glow--amber"></div>
        <div class="landing-hero__glow landing-hero__glow--blue"></div>

        <div class="landing-container landing-hero__grid">
            <div class="landing-hero__copy">
                <div class="landing-eyebrow">
                    <img src="{{ asset('storage/bsi.png') }}" alt="Logo BSI" style="height: 40px; width: auto;">
                    Universitas BSI
                </div>

                <h1 class="landing-hero__title">
                    Temukan Barangmu yang <span>Hilang</span> & Pulihkan Kepercayaan
                </h1>

                <p class="landing-hero__text">
                    Platform Lost & Found kampus terpadu. Laporkan barang hilang atau temuan dengan cepat,
                    dan bantu sesama mahasiswa mendapatkan kembali miliknya.
                </p>

                <div class="landing-actions">
                    <a href="{{ route('reports.temuan') }}" class="landing-btn landing-btn--primary">
                        Cari Barang Temuan
                    </a>
                    @auth
                        <a href="{{ route('reports.create') }}" class="landing-btn landing-btn--glass">
                            <span>+</span> Buat Laporan
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="landing-btn landing-btn--glass">
                            <span>+</span> Buat Laporan
                        </a>
                    @endauth
                </div>
            </div>

            <div class="landing-stats" aria-label="Statistik Find.It">
                <div class="landing-stat landing-stat--glass">
                    <div class="landing-stat__icon landing-stat__icon--blue">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/><path d="m9 11 2 2 4-5"/></svg>
                    </div>
                    <div class="landing-stat__value">{{ $countTemuan }}</div>
                    <div class="landing-stat__label">Barang Temuan</div>
                </div>

                <div class="landing-stat landing-stat--amber">
                    <div class="landing-stat__icon landing-stat__icon--amber">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 17h.01"/></svg>
                    </div>
                    <div class="landing-stat__value">{{ $countHilang }}</div>
                    <div class="landing-stat__label">Barang Hilang</div>
                </div>

                <div class="landing-stat landing-stat--wide landing-stat--glass">
                    <div>
                        <div class="landing-stat__icon landing-stat__icon--green">
                            <svg viewBox="0 0 24 24"><path d="M6 12h4l2 3 4-8"/><path d="M20 12a8 8 0 1 1-2.34-5.66"/></svg>
                        </div>
                        <div class="landing-stat__value">{{ $countSelesai }}</div>
                        <div class="landing-stat__label">Berhasil Kembali ke Pemilik</div>
                    </div>
                    <div class="landing-avatar-stack" aria-hidden="true">
                        <span>RA</span><span>SW</span><span>BT</span><span>+{{ max($countSelesai, 3) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-feed">
        <div class="landing-container">
            <div class="landing-section">
                <div class="landing-section__head">
                    <div>
                        <h2>Barang Temuan Terbaru</h2>
                        <p>Barang yang baru saja ditemukan di lingkungan kampus</p>
                    </div>
                    <a href="{{ route('reports.temuan') }}">Lihat semua <span>-></span></a>
                </div>

                @if($laporanTemuan->count() > 0)
                    <div class="landing-card-grid">
                        @foreach($laporanTemuan as $report)
                            <a href="{{ route('reports.show', $report->id) }}" class="landing-item-card">
                                <div class="landing-item-card__media">
                                    @if($imageFor($report))
                                        <img src="{{ $imageFor($report) }}" alt="{{ $report->nama_barang }}">
                                    @else
                                        <div class="landing-item-card__placeholder">
                                            <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                        </div>
                                    @endif
                                    <span class="landing-chip landing-chip--found">Temuan</span>
                                </div>
                                <div class="landing-item-card__body">
                                    <h3>{{ $report->nama_barang }}</h3>
                                    <p>{{ $report->lokasi }}</p>
                                    <div class="landing-item-card__meta">
                                        <span>{{ optional($report->category)->nama_category ?? 'Tanpa kategori' }}</span>
                                        <span>{{ $report->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="landing-empty">
                        <svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/><path d="M9 13h8"/><path d="M9 17h6"/></svg>
                        <p>Belum ada barang temuan yang diverifikasi.</p>
                    </div>
                @endif
            </div>

            <div class="landing-section">
                <div class="landing-section__head">
                    <div>
                        <h2>Barang Hilang Terbaru</h2>
                        <p>Mungkin Anda melihat barang-barang yang sedang dicari ini?</p>
                    </div>
                    <a href="{{ route('reports.hilang') }}">Lihat semua <span>-></span></a>
                </div>

                @if($laporanHilang->count() > 0)
                    <div class="landing-card-grid">
                        @foreach($laporanHilang as $report)
                            <a href="{{ route('reports.show', $report->id) }}" class="landing-item-card">
                                <div class="landing-item-card__media">
                                    @if($imageFor($report))
                                        <img src="{{ $imageFor($report) }}" alt="{{ $report->nama_barang }}">
                                    @else
                                        <div class="landing-item-card__placeholder">
                                            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                        </div>
                                    @endif
                                    <span class="landing-chip landing-chip--lost">Hilang</span>
                                </div>
                                <div class="landing-item-card__body">
                                    <h3>{{ $report->nama_barang }}</h3>
                                    <p>{{ $report->lokasi }}</p>
                                    <div class="landing-item-card__meta">
                                        <span>{{ optional($report->category)->nama_category ?? 'Tanpa kategori' }}</span>
                                        <span>{{ $report->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                        <a href="{{ auth()->check() ? route('reports.create') : route('register') }}" class="landing-report-slot">
                            <span>+</span>
                            Laporkan barang hilang milik Anda di sini
                        </a>
                    </div>
                @else
                    <div class="landing-empty">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <p>Belum ada laporan barang hilang.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="landing-success">
        <div class="landing-container">
            <div class="landing-success__intro">
                <span>Kisah Sukses Komunitas</span>
                <h2>Karena Kejujuran Itu Menular</h2>
                <p>Melihat barang-barang kembali ke pemiliknya adalah alasan kami ada. Bangun kampus yang lebih baik bersama-sama.</p>
            </div>

            <div class="landing-story-grid">
                <article class="landing-story">
                    <div class="landing-story__person"><span>RA</span><div><strong>Rizky A.</strong><small>Mahasiswa Teknik</small></div></div>
                    <p>"Kunci motor saya jatuh di parkiran. Berkat Find.It, hanya butuh 2 jam sampai seseorang menemukannya."</p>
                    <div class="landing-story__status">Status: Dikembalikan</div>
                </article>
                <article class="landing-story landing-story--featured">
                    <div class="landing-story__person"><span>SW</span><div><strong>Siska W.</strong><small>Mahasiswa Ekonomi</small></div></div>
                    <p>"iPad saya tertinggal di mushola. Jujur sudah pasrah, tapi ternyata ada yang lapor di sini."</p>
                    <div class="landing-story__status">Status: Dikembalikan</div>
                </article>
                <article class="landing-story">
                    <div class="landing-story__person"><span>BT</span><div><strong>Budi T.</strong><small>Mahasiswa Ilmu Komputer</small></div></div>
                    <p>"Nemuin kartu mahasiswa di jalan. Pemiliknya langsung kontak saya sore harinya. Senang bisa bantu."</p>
                    <div class="landing-story__status">Status: Dikembalikan</div>
                </article>
            </div>
        </div>
    </section>
</div>
</x-app-layout>
