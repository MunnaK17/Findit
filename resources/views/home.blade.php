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
                                            <span class="landing-carousel-badge landing-carousel-badge--found">Temuan</span>
                                        </div>
                                        <div class="landing-carousel-card__body">
                                            <h3>{{ $report->nama_barang }}</h3>
                                            <p>📍 {{ $report->lokasi }}</p>
                                            <div class="landing-carousel-card__meta">
                                                <span>{{ optional($report->category)->nama_category ?? 'Tanpa kategori' }}</span>
                                                <span>{{ $report->tanggal_kejadian->format('d M Y') }}</span>
                                            </div>
                                            <div class="landing-carousel-card__time">{{ $report->created_at->diffForHumans() }}</div>
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
                                            <span class="landing-carousel-badge landing-carousel-badge--lost">Hilang</span>
                                        </div>
                                        <div class="landing-carousel-card__body">
                                            <h3>{{ $report->nama_barang }}</h3>
                                            <p>📍 {{ $report->lokasi }}</p>
                                            <div class="landing-carousel-card__meta">
                                                <span>{{ optional($report->category)->nama_category ?? 'Tanpa kategori' }}</span>
                                                <span>{{ $report->tanggal_kejadian->format('d M Y') }}</span>
                                            </div>
                                            <div class="landing-carousel-card__time">{{ $report->created_at->diffForHumans() }}</div>
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

    {{-- FAQ Section --}}
    <section class="landing-faq">
        <div class="landing-container">
            <div class="landing-faq__intro">
                <span>Pertanyaan Umum</span>
                <h2>Ada Pertanyaan? Kami Jawab!</h2>
                <p>Temukan jawaban untuk pertanyaan yang sering diajukan tentang FindIt.</p>
            </div>

            <div class="landing-faq__list">
                {{-- FAQ Item 1 --}}
                <details class="landing-faq__item">
                    <summary class="landing-faq__question">
                        <span>Bagaimana cara melaporkan barang yang saya temukan?</span>
                        <svg class="landing-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="landing-faq__answer">
                        <p>Jika Anda menemukan barang milik orang lain, klik tombol "Buat Laporan" di halaman utama. Pilih opsi "Saya Menemukan", isi detail barang seperti nama, kategori, lokasi temuan, dan tanggal kejadian. Setelah submit, laporan Anda akan diverifikasi oleh admin sebelum ditampilkan.</p>
                    </div>
                </details>

                {{-- FAQ Item 2 --}}
                <details class="landing-faq__item">
                    <summary class="landing-faq__question">
                        <span>Apakah laporan saya dijamin aman?</span>
                        <svg class="landing-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="landing-faq__answer">
                        <p>Ya! Setiap laporan diverifikasi oleh admin sebelum dipublikasikan. Kami juga tidak mempublikasikan data pribadi Anda (nama, NIM, nomor HP) secara terbuka. Data hanya dibagikan kepada pihak yang berhak setelah klaim mereka diverifikasi.</p>
                    </div>
                </details>

                {{-- FAQ Item 3 --}}
                <details class="landing-faq__item">
                    <summary class="landing-faq__question">
                        <span>Berapa lama proses verifikasi klaim?</span>
                        <svg class="landing-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="landing-faq__answer">
                        <p>Proses verifikasi klaim biasanya memakan waktu 1x24 jam. Admin akan memeriksa detail klaim Anda dan menghubungi pihak yang menemukan barang. Setelah diverifikasi, Anda akan mendapat notifikasi melalui email dan WhatsApp.</p>
                    </div>
                </details>

                {{-- FAQ Item 4 --}}
                <details class="landing-faq__item">
                    <summary class="landing-faq__question">
                        <span>Bagaimana cara mengklaim barang yang saya temukan?</span>
                        <svg class="landing-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="landing-faq__answer">
                        <p>Jika Anda kehilangan barang, buka halaman "Barang Hilang" dan cari barang Anda. Klik detail barang, lalu pilih "Klaim Barang". Isikan pesan yang menjelaskan secara detail mengapa barang ini milik Anda (ciri khusus, tanggal kehilangan, isi barang, dll). Admin akan memverifikasi klaim Anda.</p>
                    </div>
                </details>

                {{-- FAQ Item 5 --}}
                <details class="landing-faq__item">
                    <summary class="landing-faq__question">
                        <span>Apakah data pribadi saya aman di FindIt?</span>
                        <svg class="landing-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="landing-faq__answer">
                        <p>Kami sangat menjaga privasi Anda. Data pribadi seperti nama, NIM, dan nomor HP hanya digunakan untuk keperluan verifikasi klaim dan komunikasi terkait barang. Data tidak akan dibagikan ke pihak ketiga yang tidak berkepentingan.</p>
                    </div>
                </details>
            </div>
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
    gap: 10px;
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

.landing-carousel-card:active {
    transform: scale(0.97);
}

.landing-carousel-card__media {
    position: relative;
    width: 100%;
    height: 130px;
    background: #f8f9fa;
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
    width: 36px;
    height: 36px;
    stroke: #6b7280;
    fill: none;
    stroke-width: 1.5;
    opacity: 0.6;
}

/* Badge in image */
.landing-carousel-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 600;
}

.landing-carousel-badge--found {
    background: #10b981;
    color: #fff;
}

.landing-carousel-badge--lost {
    background: #ef4444;
    color: #fff;
}

.landing-carousel-card__body {
    padding: 12px 14px 14px 14px;
}

.landing-carousel-card__body h3 {
    font-size: 14px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 5px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.landing-carousel-card__body p {
    font-size: 11px;
    color: #6b7280;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.landing-carousel-card__meta {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #9ca3af;
    margin-bottom: 2px;
}

.landing-carousel-card__time {
    font-size: 9px;
    color: #9ca3af;
}

/* Dots Indicator */
.landing-carousel-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 6px;
}

.landing-carousel-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    transition: all 0.2s ease;
    cursor: pointer;
}

.landing-carousel-dot.active {
    background: #059669;
    width: 20px;
    border-radius: 4px;
}

/* ========== FAQ Section ========== */
.landing-faq {
    padding: 80px 0;
    background: #f8fafc;
}

.landing-faq__intro {
    text-align: center;
    margin-bottom: 48px;
}

.landing-faq__intro span {
    display: inline-block;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 16px;
}

.landing-faq__intro h2 {
    font-size: 32px;
    font-weight: 800;
    color: #1f2937;
    margin: 0 0 12px 0;
}

.landing-faq__intro p {
    font-size: 16px;
    color: #6b7280;
    margin: 0;
}

.landing-faq__list {
    max-width: 720px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.landing-faq__item {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.2s ease;
}

.landing-faq__item:hover {
    border-color: #059669;
    box-shadow: 0 2px 12px rgba(5, 150, 105, 0.08);
}

.landing-faq__item[open] {
    border-color: #059669;
}

.landing-faq__question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 20px;
    cursor: pointer;
    list-style: none;
    font-weight: 600;
    font-size: 15px;
    color: #1f2937;
    gap: 16px;
}

.landing-faq__question::-webkit-details-marker {
    display: none;
}

.landing-faq__question span {
    flex: 1;
}

.landing-faq__icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    transition: transform 0.3s ease;
    color: #6b7280;
}

.landing-faq__item[open] .landing-faq__icon {
    transform: rotate(180deg);
    color: #059669;
}

.landing-faq__answer {
    padding: 0 20px 20px 20px;
    border-top: 1px solid #f3f4f6;
    margin-top: 0;
}

.landing-faq__answer p {
    font-size: 14px;
    line-height: 1.7;
    color: #4b5563;
    margin: 16px 0 0 0;
    padding-top: 16px;
}

/* Responsive FAQ */
@media (max-width: 768px) {
    .landing-faq {
        padding: 48px 0;
    }
    .landing-faq__intro h2 {
        font-size: 24px;
    }
    .landing-faq__question {
        font-size: 14px;
        padding: 14px 16px;
    }
    .landing-faq__answer {
        padding: 0 16px 16px 16px;
    }
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