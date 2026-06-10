<x-app-layout>

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Barang Hilang</div>
            <div class="page-sub">Daftar barang yang sedang dicari pemiliknya</div>
        </div>
        @auth
            <a href="{{ route('reports.create') }}" class="btn btn-navy px-4">
                + Laporkan Barang Hilang
            </a>
        @endauth
    </div>

    {{-- Search & Filter --}}
    <div class="findit-card mb-4" style="padding:14px 16px;">
        <form method="GET" action="{{ route('reports.hilang') }}">
            <div class="d-flex gap-2 flex-wrap">
                <div class="search-bar-wrap flex-grow-1">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama barang...">
                </div>
                <select name="category" class="form-select" style="width:auto;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_category }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-navy px-3">Cari</button>
                @if(request('search') || request('category'))
                    <a href="{{ route('reports.hilang') }}" class="btn btn-outline-findit px-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if($reports->count() > 0)
        {{-- Desktop Grid (md+) --}}
        <div class="d-none d-md-block">
            <div class="row g-3">
                @foreach($reports as $report)
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ route('reports.show', $report->id) }}" class="item-card">
                            <div class="item-card-img">
                                @if($report->foto_barang)
                                    <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                         alt="{{ $report->nama_barang }}"
                                         style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                @endif
                            </div>
                            <div class="item-card-body">
                                <div class="item-card-name">{{ $report->nama_barang }}</div>
                                <div class="item-card-loc">📍 {{ $report->lokasi }}</div>
                                <div style="font-size:10px;color:var(--text-3);margin-bottom:6px;">
                                    {{ $report->category->nama_category }} ·
                                    {{ $report->tanggal_kejadian->format('d M Y') }}
                                </div>
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
        </div>

        {{-- Mobile Carousel (< md) --}}
        <div class="d-md-none">
            <div class="carousel-wrapper">
                <div class="carousel-track" id="carousel-hilang">
                    @foreach($reports as $report)
                        <a href="{{ route('reports.show', $report->id) }}" class="carousel-card">
                            <div class="carousel-card-img">
                                @if($report->foto_barang)
                                    <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                         alt="{{ $report->nama_barang }}">
                                @else
                                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                @endif
                                <span class="carousel-badge carousel-badge--lost">Hilang</span>
                            </div>
                            <div class="carousel-card-body">
                                <div class="carousel-card-name">{{ $report->nama_barang }}</div>
                                <div class="carousel-card-loc">📍 {{ $report->lokasi }}</div>
                                <div class="carousel-card-date">
                                    {{ $report->category->nama_category }} · {{ $report->tanggal_kejadian->format('d M Y') }}
                                </div>
                                <div class="carousel-card-time">
                                    {{ $report->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="carousel-dots" id="carousel-hilang-dots"></div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $reports->withQueryString()->links() }}
        </div>
    @else
        <div class="findit-card p-5 text-center">
            <div style="font-size:32px;margin-bottom:12px;">🔍</div>
            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                Tidak ada laporan ditemukan
            </div>
            <div style="font-size:12px;color:var(--text-2);">
                Coba ubah kata kunci pencarian atau filter kategori
            </div>
        </div>
    @endif

</x-app-layout>

{{-- Carousel Styles & Scripts --}}
<style>
/* Carousel Mobile Styles */
.carousel-wrapper {
    overflow: hidden;
    padding: 0 16px 16px 16px;
    margin: 0 -16px;
}

.carousel-track {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding: 0 16px 8px 16px;
}

.carousel-track::-webkit-scrollbar {
    display: none;
}

.carousel-card {
    flex: 0 0 calc(70% - 10px);
    scroll-snap-align: start;
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 0.5px solid rgba(0,0,0,0.06);
    transition: transform 0.2s ease;
}

.carousel-card:active {
    transform: scale(0.97);
}

.carousel-card-img {
    width: 100%;
    height: 130px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.carousel-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carousel-card-img svg {
    width: 36px;
    height: 36px;
    stroke: #dc2626;
    fill: none;
    stroke-width: 1.5;
    opacity: 0.6;
}

/* Badge in image */
.carousel-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 600;
}

.carousel-badge--found {
    background: #10b981;
    color: #fff;
}

.carousel-badge--lost {
    background: #ef4444;
    color: #fff;
}

.carousel-card-body {
    padding: 12px 14px 14px 14px;
}

.carousel-card-name {
    font-size: 14px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.carousel-card-loc {
    font-size: 11px;
    color: #6b7280;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.carousel-card-date {
    font-size: 10px;
    color: #9ca3af;
    margin-bottom: 2px;
}

.carousel-card-time {
    font-size: 9px;
    color: #9ca3af;
}

/* Dots Indicator */
.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 6px;
}

.carousel-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    transition: all 0.2s ease;
    cursor: pointer;
}

.carousel-dot.active {
    background: #059669;
    width: 20px;
    border-radius: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initCarousel('carousel-hilang', 'carousel-hilang-dots');
});

function initCarousel(trackId, dotsId) {
    const track = document.getElementById(trackId);
    const dotsContainer = document.getElementById(dotsId);
    if (!track || !dotsContainer) return;

    const cards = track.querySelectorAll('.carousel-card');
    const totalCards = cards.length;

    if (totalCards <= 1) return;

    // Create dots
    dotsContainer.innerHTML = '';
    for (let i = 0; i < totalCards; i++) {
        const dot = document.createElement('div');
        dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.addEventListener('click', () => scrollToCard(i));
        dotsContainer.appendChild(dot);
    }

    const dots = dotsContainer.querySelectorAll('.carousel-dot');

    // Update dots on scroll
    track.addEventListener('scroll', () => {
        const scrollLeft = track.scrollLeft;
        const cardWidth = cards[0].offsetWidth + 12; // card width + gap
        const activeIndex = Math.round(scrollLeft / cardWidth);
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === activeIndex);
        });
    });

    // Scroll to card
    function scrollToCard(index) {
        const cardWidth = cards[0].offsetWidth + 12;
        track.scrollTo({
            left: cardWidth * index,
            behavior: 'smooth'
        });
    }
}
</script>
