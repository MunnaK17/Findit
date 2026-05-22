<x-app-layout>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('my.reports') }}"
                   style="color:var(--text-3);text-decoration:none;font-size:12px;">
                    ← Kembali
                </a>
                <div>
                    <div class="page-title">Edit Laporan</div>
                    <div class="page-sub">{{ $report->nama_barang }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('reports.update', $report->id) }}"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

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
                                   value="{{ old('nama_barang', $report->nama_barang) }}">
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="id_category"
                                    class="form-select @error('id_category') is-invalid @enderror">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('id_category', $report->id_category) == $cat->id ? 'selected' : '' }}>
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
                                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $report->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi"
                                   class="form-control @error('lokasi') is-invalid @enderror"
                                   value="{{ old('lokasi', $report->lokasi) }}">
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Kejadian</label>
                            <input type="date" name="tanggal_kejadian"
                                   class="form-control @error('tanggal_kejadian') is-invalid @enderror"
                                   value="{{ old('tanggal_kejadian', $report->tanggal_kejadian->format('Y-m-d')) }}"
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
                        Foto Barang
                    </div>

                    @if($report->foto_barang)
                        <div class="mb-3">
                            <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                 style="height:120px;border-radius:var(--r);object-fit:cover;border:0.5px solid var(--border);">
                            <div style="font-size:10px;color:var(--text-3);margin-top:4px;">
                                Foto saat ini
                            </div>
                        </div>
                    @endif

                    <div style="background:var(--bg);border:1.5px dashed var(--border);
                                border-radius:var(--r);padding:16px;text-align:center;">
                        <div style="font-size:11px;color:var(--text-2);margin-bottom:8px;">
                            Upload foto baru (kosongkan jika tidak ingin mengubah)
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
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('my.reports') }}" class="btn btn-outline-findit px-4 py-2">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>