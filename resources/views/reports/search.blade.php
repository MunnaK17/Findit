<x-app-layout>

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <div class="page-title">Cari Barang</div>
            <div class="page-sub">Pencarian terpadu barang hilang &amp; temuan di seluruh 11 cabang UBSI</div>
        </div>
        @auth
            <a href="{{ route('reports.create') }}" class="btn btn-navy px-4">
                + Buat Laporan
            </a>
        @endauth
    </div>

    {{-- Filter & Search Box --}}
    <div class="findit-card mb-4" style="padding:16px 18px;">
        <form method="GET" action="{{ route('reports.search') }}" id="searchFilterForm">
            {{-- Filter Chips Jenis Laporan --}}
            <input type="hidden" name="jenis" id="jenisInput" value="{{ request('jenis', '') }}">
            <div class="d-flex gap-2 flex-wrap align-items-center mb-3">
                @php $activeJenis = request('jenis', ''); @endphp
                <button type="button" onclick="setJenis('')"
                        style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;border:none;cursor:pointer;
                               {{ $activeJenis === '' ? 'background:var(--navy);color:#fff;' : 'background:var(--bg);color:var(--text-2);border:1px solid var(--border);' }}">
                    Semua Laporan
                </button>
                <button type="button" onclick="setJenis('hilang')"
                        style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;border:none;cursor:pointer;
                               {{ $activeJenis === 'hilang' ? 'background:#dc2626;color:#fff;' : 'background:var(--bg);color:var(--text-2);border:1px solid var(--border);' }}">
                    Barang Hilang
                </button>
                <button type="button" onclick="setJenis('temuan')"
                        style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;border:none;cursor:pointer;
                               {{ $activeJenis === 'temuan' ? 'background:#059669;color:#fff;' : 'background:var(--bg);color:var(--text-2);border:1px solid var(--border);' }}">
                    Barang Temuan
                </button>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <div class="search-bar-wrap flex-grow-1" style="min-width:220px;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama barang, ciri-ciri, atau lokasi...">
                </div>

                {{-- Filter Cabang Kampus --}}
                <select name="campus" class="form-select" style="width:auto;min-width:200px;" onchange="this.form.submit()">
                    <option value="">Semua Cabang UBSI</option>
                    @foreach($campuses as $c)
                        <option value="{{ $c->id }}" {{ (string) request('campus') === (string) $c->id ? 'selected' : '' }}>
                            {{ $c->nama_kampus }} ({{ $c->kota }})
                        </option>
                    @endforeach
                </select>

                {{-- Filter Kategori --}}
                <select name="category" class="form-select" style="width:auto;min-width:170px;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_category }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-navy px-4">Cari</button>
                @if(request()->anyFilled(['search', 'category', 'campus', 'jenis']))
                    <a href="{{ route('reports.search') }}" class="btn btn-outline-findit px-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Daftar Hasil --}}
    @if($reports->count() > 0)
        <div class="row g-3">
            @foreach($reports as $report)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <a href="{{ route('reports.show', $report->id) }}" class="item-card">
                        <div class="item-card-img">
                            @if($report->foto_barang)
                                <img src="{{ $report->fotoUrl() }}"
                                     alt="{{ $report->nama_barang }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            @endif
                        </div>
                        <div class="item-card-body">
                            <div class="item-card-name">{{ $report->nama_barang }}</div>
                            @if($report->campus)
                                <div style="font-size:11px;font-weight:600;color:var(--navy);margin-bottom:2px;">
                                    🏢 {{ $report->campus->nama_kampus }}
                                </div>
                            @endif
                            <div class="item-card-loc">📍 {{ $report->lokasi }}</div>
                            <div style="font-size:10px;color:var(--text-3);margin-bottom:6px;">
                                {{ $report->category->nama_category ?? 'Tanpa kategori' }} ·
                                {{ $report->tanggal_kejadian->format('d M Y') }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                @if($report->status === 'completed')
                                    <span class="findit-badge b-navy">Kembali ke Pemilik</span>
                                @elseif($report->jenis_laporan === 'hilang')
                                    <span class="findit-badge b-red">Hilang</span>
                                @else
                                    <span class="findit-badge b-green">Temuan</span>
                                @endif
                                <span style="font-size:10px;color:var(--text-3);">
                                    {{ $report->created_at?->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $reports->links() }}
        </div>
    @else
        <div class="findit-card p-5 text-center">
            <div style="font-size:32px;margin-bottom:12px;">🔍</div>
            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                Tidak ada barang yang cocok
            </div>
            <div style="font-size:12px;color:var(--text-2);">
                Coba ganti kata kunci pencarian, kategori, atau cabang kampus.
            </div>
        </div>
    @endif

    <script>
        function setJenis(val) {
            document.getElementById('jenisInput').value = val;
            document.getElementById('searchFilterForm').submit();
        }
    </script>

</x-app-layout>
