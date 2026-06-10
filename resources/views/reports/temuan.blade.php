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
        <div class="row g-3">
            @foreach($reports as $report)
                <div class="col-md-4 col-lg-3">
                    <a href="{{ route('reports.show', $report->id) }}" class="item-card">
                        <div class="item-card-img">
                            @if($report->foto_barang)
                                <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                     class="lightbox-img"
                                     alt="{{ $report->nama_barang }}">
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