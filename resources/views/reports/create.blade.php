<x-app-layout>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('my.reports') }}"
                   style="color:var(--text-3);text-decoration:none;font-size:12px;">
                    ← Kembali
                </a>
                <div>
                    <div class="page-title">Buat Laporan Baru</div>
                    <div class="page-sub">Isi detail barang hilang atau temuan</div>
                </div>
            </div>

            <form method="POST" action="{{ route('reports.store') }}"
                  enctype="multipart/form-data">
                @csrf

                {{-- Jenis Laporan --}}
                <div class="findit-card p-4 mb-3">
                    <div style="font-size:11px;font-weight:700;color:var(--text-3);
                                text-transform:uppercase;letter-spacing:0.07em;margin-bottom:14px;">
                        Jenis Laporan
                    </div>

                    <div class="d-flex gap-3">
                        <label style="flex:1;cursor:pointer;">
                            <input type="radio" name="jenis_laporan" value="hilang"
                                   class="d-none" {{ old('jenis_laporan') === 'hilang' ? 'checked' : '' }}>
                            <div class="jenis-option" id="opt-hilang"
                                 style="border:1.5px solid var(--border);border-radius:var(--r);
                                        padding:16px;text-align:center;transition:all .15s;">
                                <div style="font-size:24px;margin-bottom:6px;">😟</div>
                                <div style="font-size:13px;font-weight:700;color:var(--text);">Saya Kehilangan</div>
                                <div style="font-size:10px;color:var(--text-2);">Barang saya hilang</div>
                            </div>
                        </label>
                        <label style="flex:1;cursor:pointer;">
                            <input type="radio" name="jenis_laporan" value="temuan"
                                   class="d-none" {{ old('jenis_laporan') === 'temuan' ? 'checked' : '' }}>
                            <div class="jenis-option" id="opt-temuan"
                                 style="border:1.5px solid var(--border);border-radius:var(--r);
                                        padding:16px;text-align:center;transition:all .15s;">
                                <div style="font-size:24px;margin-bottom:6px;">🎉</div>
                                <div style="font-size:13px;font-weight:700;color:var(--text);">Saya Menemukan</div>
                                <div style="font-size:10px;color:var(--text-2);">Saya menemukan barang orang</div>
                            </div>
                        </label>
                    </div>
                    @error('jenis_laporan')
                        <div style="font-size:11px;color:var(--danger);margin-top:8px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Info Barang --}}
                <div class="findit-card p-4 mb-3">
                    <div style="font-size:11px;font-weight:700;color:var(--text-3);
                                text-transform:uppercase;letter-spacing:0.07em;margin-bottom:14px;">
                        Informasi Barang
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang"
                                   class="form-control @error('nama_barang') is-invalid @enderror"
                                   value="{{ old('nama_barang') }}"
                                   placeholder="Contoh: Dompet kulit coklat">
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="id_category"
                                    class="form-select @error('id_category') is-invalid @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('id_category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" rows="3"
                                      class="form-control @error('deskripsi') is-invalid @enderror"
                                      placeholder="Jelaskan ciri-ciri barang secara detail...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Kejadian</label>
                            <input type="text" name="lokasi"
                                   class="form-control @error('lokasi') is-invalid @enderror"
                                   value="{{ old('lokasi') }}"
                                   placeholder="Contoh: Kantin Gedung B">
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Kejadian</label>
                            <input type="date" name="tanggal_kejadian"
                                   class="form-control @error('tanggal_kejadian') is-invalid @enderror"
                                   value="{{ old('tanggal_kejadian') }}"
                                   max="{{ date('Y-m-d') }}">
                            @error('tanggal_kejadian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Foto --}}
                <div class="findit-card p-4 mb-4">
                    <div style="font-size:11px;font-weight:700;color:var(--text-3);
                                text-transform:uppercase;letter-spacing:0.07em;margin-bottom:14px;">
                        Foto Barang (Opsional)
                    </div>
                    <div style="background:var(--bg);border:1.5px dashed var(--border);
                                border-radius:var(--r);padding:24px;text-align:center;">
                        <svg viewBox="0 0 24 24" style="width:28px;height:28px;stroke:var(--text-3);fill:none;stroke-width:1.5;stroke-linecap:round;margin-bottom:8px;">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <div style="font-size:11px;color:var(--text-2);margin-bottom:8px;">
                            Upload foto barang (JPG, PNG, max 2MB)
                        </div>
                        <input type="file" name="foto_barang"
                               class="form-control @error('foto_barang') is-invalid @enderror"
                               accept="image/*"
                               style="max-width:260px;margin:0 auto;">
                        @error('foto_barang')
                            <div style="font-size:11px;color:var(--danger);margin-top:6px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-navy px-5 py-2">
                        Kirim Laporan
                    </button>
                    <a href="{{ route('my.reports') }}" class="btn btn-outline-findit px-4 py-2">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

    {{-- Script untuk jenis laporan toggle --}}
    <script>
        document.querySelectorAll('input[name="jenis_laporan"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.jenis-option').forEach(el => {
                    el.style.borderColor = 'var(--border)';
                    el.style.background = 'transparent';
                });
                if (this.checked) {
                    const opt = this.value === 'hilang'
                        ? document.getElementById('opt-hilang')
                        : document.getElementById('opt-temuan');
                    opt.style.borderColor = 'var(--navy)';
                    opt.style.background = 'var(--navy-pale)';
                }
            });
        });

        // Set initial state
        document.querySelectorAll('input[name="jenis_laporan"]').forEach(radio => {
            if (radio.checked) radio.dispatchEvent(new Event('change'));
        });
    </script>

</x-app-layout>