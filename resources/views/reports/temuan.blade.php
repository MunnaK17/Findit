<x-app-layout>

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Barang Temuan</div>
            <div class="page-sub">Barang yang ditemukan dan menunggu pemiliknya</div>
        </div>
        @auth
            <a href="{{ route('reports.create') }}" class="btn btn-navy px-4">
                + Laporkan Barang Temuan
            </a>
        @endauth
    </div>

    {{-- Search & Filter --}}
    <div class="findit-card mb-4" style="padding:14px 16px;">
        <form method="GET" action="{{ route('reports.temuan') }}">
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
                    <a href="{{ route('reports.temuan') }}" class="btn btn-outline-findit px-3">Reset</a>
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
                                    <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
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
        </div>

        {{-- Mobile Carousel (< md) --}}
        <div class="d-md-none">
            <div class="carousel-wrapper">
                <div class="carousel-track" id="carousel-temuan">
                    @foreach($reports as $report)
                        <a href="{{ route('reports.show', $report->id) }}" class="carousel-card">
                            <div class="carousel-card-img">
                                @if($report->foto_barang)
                                    <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                         alt="{{ $report->nama_barang }}"
                                         style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                @endif
                            </div>
                            <div class="carousel-card-body">
                                <div class="carousel-card-name">{{ $report->nama_barang }}</div>
                                <div class="carousel-card-loc">📍 {{ $report->lokasi }}</div>
                                <div style="font-size:9px;color:var(--text-3);margin-bottom:4px;">
                                    {{ $report->category->nama_category }} ·
                                    {{ $report->tanggal_kejadian->format('d M Y') }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="findit-badge b-green">Temuan</span>
                                    <span style="font-size:9px;color:var(--text-3);">
                                        {{ $report->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="carousel-dots" id="carousel-temuan-dots"></div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $reports->withQueryString()->links() }}
        </div>
    @else
        <div class="findit-card p-5 text-center">
            <div style="font-size:32px;margin-bottom:12px;">📦</div>
            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                Tidak ada barang temuan
            </div>
            <div style="font-size:12px;color:var(--text-2);">
                Belum ada barang temuan yang diverifikasi saat ini
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
    gap: 12px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 8px;
}

.carousel-track::-webkit-scrollbar {
    display: none;
}

.carousel-card {
    flex: 0 0 calc(75% - 12px);
    scroll-snap-align: start;
    background: #fff;
    border-radius: 12px;
    border: 0.5px solid var(--border);
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.carousel-card:active {
    transform: scale(0.98);
}

.carousel-card-img {
    width: 100%;
    height: 140px;
    background: var(--navy-pale);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.carousel-card-img svg {
    width: 32px;
    height: 32px;
    stroke: var(--navy-mid);
    fill: none;
    stroke-width: 1.5;
}

.carousel-card-body {
    padding: 12px;
}

.carousel-card-name {
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.carousel-card-loc {
    font-size: 10px;
    color: var(--text-2);
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Dots Indicator */
.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 8px;
}

.carousel-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--border);
    transition: all 0.2s ease;
    cursor: pointer;
}

.carousel-dot.active {
    background: var(--navy);
    width: 20px;
    border-radius: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initCarousel('carousel-temuan', 'carousel-temuan-dots');
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
