<x-app-layout>
@php
    $countTemuan = \App\Models\Report::where('jenis_laporan', 'temuan')->where('status', 'approved')->count();
    $countHilang = \App\Models\Report::where('jenis_laporan', 'hilang')->where('status', 'approved')->count();
    $countSelesai = \App\Models\Report::where('status', 'completed')->count();
    $testimonials = \App\Models\Testimonial::with('user')->latest()->limit(3)->get();

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
            {{-- Barang Temuan Terbaru --}}
            <div class="landing-section">
                <div class="landing-section__head">
                    <div>
                        <h2>Barang Temuan Terbaru</h2>
                        <p>Barang yang baru saja ditemukan di lingkungan kampus</p>
                    </div>
                    <a href="{{ route('reports.temuan') }}">Lihat semua <span>-></span></a>
                </div>

                @if($laporanTemuan->count() > 0)
                    {{-- Desktop Grid (md+) --}}
                    <div class="d-none d-md-block">
                        <div class="landing-card-grid">
                            @foreach($laporanTemuan as $report)
                                <a href="{{ route('reports.show', $report->id) }}" class="landing-item-card">
                                    <div class="landing-item-card__media">
                                        @if($imageFor($report))
                                            <img src="{{ $imageFor($report) }}" class="lightbox-img" alt="{{ $report->nama_barang }}">
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
                    </div>

                    {{-- Mobile Carousel (< md) --}}
                    <div class="d-md-none">
                        <div class="landing-carousel-wrapper">
                            <div class="landing-carousel-track" id="carousel-home-temuan">
                                @foreach($laporanTemuan as $report)
                                    <a href="{{ route('reports.show', $report->id) }}" class="landing-carousel-card">
                                        <div class="landing-carousel-card__media">
                                            @if($imageFor($report))
                                                <img src="{{ $imageFor($report) }}" alt="{{ $report->nama_barang }}">
                                            @else
                                                <div class="landing-carousel-card__placeholder">
                                                    <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                                </div>
                                            @endif
                                            <span class="landing-chip landing-chip--found">Temuan</span>
                                        </div>
                                        <div class="landing-carousel-card__body">
                                            <h3>{{ $report->nama_barang }}</h3>
                                            <p>{{ $report->lokasi }}</p>
                                            <div class="landing-carousel-card__meta">
                                                <span>{{ optional($report->category)->nama_category ?? 'Tanpa kategori' }}</span>
                                                <span>{{ $report->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="landing-carousel-dots" id="carousel-home-temuan-dots"></div>
                        </div>
                    </div>
                @else
                    <div class="landing-empty">
                        <svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/><path d="M9 13h8"/><path d="M9 17h6"/></svg>
                        <p>Belum ada barang temuan yang diverifikasi.</p>
                    </div>
                @endif
            </div>

            {{-- Barang Hilang Terbaru --}}
            <div class="landing-section">
                <div class="landing-section__head">
                    <div>
                        <h2>Barang Hilang Terbaru</h2>
                        <p>Mungkin Anda melihat barang-barang yang sedang dicari ini?</p>
                    </div>
                    <a href="{{ route('reports.hilang') }}">Lihat semua <span>-></span></a>
                </div>

                @if($laporanHilang->count() > 0)
                    {{-- Desktop Grid (md+) --}}
                    <div class="d-none d-md-block">
                        <div class="landing-card-grid">
                            @foreach($laporanHilang as $report)
                                <a href="{{ route('reports.show', $report->id) }}" class="landing-item-card">
                                    <div class="landing-item-card__media">
                                        @if($imageFor($report))
                                            <img src="{{ $imageFor($report) }}" class="lightbox-img" alt="{{ $report->nama_barang }}">
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
                    </div>

                    {{-- Mobile Carousel (< md) --}}
                    <div class="d-md-none">
                        <div class="landing-carousel-wrapper">
                            <div class="landing-carousel-track" id="carousel-home-hilang">
                                @foreach($laporanHilang as $report)
                                    <a href="{{ route('reports.show', $report->id) }}" class="landing-carousel-card">
                                        <div class="landing-carousel-card__media">
                                            @if($imageFor($report))
                                                <img src="{{ $imageFor($report) }}" alt="{{ $report->nama_barang }}">
                                            @else
                                                <div class="landing-carousel-card__placeholder">
                                                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                                </div>
                                            @endif
                                            <span class="landing-chip landing-chip--lost">Hilang</span>
                                        </div>
                                        <div class="landing-carousel-card__body">
                                            <h3>{{ $report->nama_barang }}</h3>
                                            <p>{{ $report->lokasi }}</p>
                                            <div class="landing-carousel-card__meta">
                                                <span>{{ optional($report->category)->nama_category ?? 'Tanpa kategori' }}</span>
                                                <span>{{ $report->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="landing-carousel-dots" id="carousel-home-hilang-dots"></div>
                        </div>
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

            @if($testimonials->count() > 0)
                <div class="landing-story-grid">
                    @foreach($testimonials as $testimonial)
                        <article class="landing-story">
                            <div class="landing-story__person">
                                <span>{{ $testimonial->getUserInitials() }}</span>
                                <div>
                                    <strong>{{ $testimonial->user->name }}</strong>
                                    <small>{{ $testimonial->user->nim ?? 'Mahasiswa' }}</small>
                                </div>
                            </div>
                            <div class="landing-story__rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span style="color: {{ $i <= $testimonial->rating ? '#ffc107' : '#dee2e6' }};">
                                        @if($i <= $testimonial->rating) ★ @else ☆ @endif
                                    </span>
                                @endfor
                            </div>
                            <p>"{{ $testimonial->isi_testimoni }}"</p>
                            <div class="landing-story__status">Status: Dikembalikan</div>
                        </article>
                    @endforeach
                </div>
            @else
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
            @endif
        </div>
    </section>
</div>

{{-- Landing Page Carousel Styles --}}
<style>
/* Landing Carousel Mobile Styles */
.landing-carousel-wrapper {
    overflow: hidden;
    padding: 0 0 16px 0;
    margin: 0 -16px;
}

.landing-carousel-track {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding: 0 16px 8px 16px;
}

.landing-carousel-track::-webkit-scrollbar {
    display: none;
}

.landing-carousel-card {
    flex: 0 0 calc(75% - 12px);
    scroll-snap-align: start;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: transform 0.2s ease;
}

.landing-carousel-card:active {
    transform: scale(0.98);
}

.landing-carousel-card__media {
    position: relative;
    width: 100%;
    height: 140px;
    background: var(--navy-pale, #f0f4ff);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.landing-carousel-card__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.landing-carousel-card__placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.landing-carousel-card__placeholder svg {
    width: 40px;
    height: 40px;
    stroke: var(--navy-mid, #3b82f6);
    fill: none;
    stroke-width: 1.5;
    opacity: 0.5;
}

.landing-chip {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
}

.landing-chip--found {
    background: rgba(16, 185, 129, 0.9);
    color: #fff;
}

.landing-chip--lost {
    background: rgba(239, 68, 68, 0.9);
    color: #fff;
}

.landing-carousel-card__body {
    padding: 14px;
}

.landing-carousel-card__body h3 {
    font-size: 14px;
    font-weight: 700;
    color: var(--text, #1f2937);
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.landing-carousel-card__body p {
    font-size: 11px;
    color: var(--text-2, #6b7280);
    margin: 0 0 8px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.landing-carousel-card__meta {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: var(--text-3, #9ca3af);
}

/* Dots Indicator */
.landing-carousel-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 4px;
}

.landing-carousel-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(0,0,0,0.15);
    transition: all 0.2s ease;
    cursor: pointer;
}

.landing-carousel-dot.active {
    background: var(--navy, #1e40af);
    width: 20px;
    border-radius: 4px;
}
</style>

{{-- Landing Page Carousel Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    initLandingCarousel('carousel-home-temuan', 'carousel-home-temuan-dots');
    initLandingCarousel('carousel-home-hilang', 'carousel-home-hilang-dots');
});

function initLandingCarousel(trackId, dotsId) {
    const track = document.getElementById(trackId);
    const dotsContainer = document.getElementById(dotsId);
    if (!track || !dotsContainer) return;

    const cards = track.querySelectorAll('.landing-carousel-card');
    const totalCards = cards.length;

    if (totalCards <= 1) return;

    // Create dots
    dotsContainer.innerHTML = '';
    for (let i = 0; i < totalCards; i++) {
        const dot = document.createElement('div');
        dot.className = 'landing-carousel-dot' + (i === 0 ? ' active' : '');
        dot.addEventListener('click', () => scrollToCard(i));
        dotsContainer.appendChild(dot);
    }

    const dots = dotsContainer.querySelectorAll('.landing-carousel-dot');

    // Update dots on scroll
    track.addEventListener('scroll', () => {
        const scrollLeft = track.scrollLeft;
        const cardWidth = cards[0].offsetWidth + 12;
        const activeIndex = Math.round(scrollLeft / cardWidth);
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === activeIndex);
        });
    });

    function scrollToCard(index) {
        const cardWidth = cards[0].offsetWidth + 12;
        track.scrollTo({
            left: cardWidth * index,
            behavior: 'smooth'
        });
    }
}
</script>
</x-app-layout>