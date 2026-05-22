<x-app-layout>

    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('reports.show', $report->id) }}"
                   style="color:var(--text-3);text-decoration:none;font-size:12px;">
                    ← Kembali ke Detail Barang
                </a>
            </div>

            {{-- Info Barang --}}
            <div class="findit-card p-4 mb-3"
                 style="background:var(--navy-pale);border-color:var(--navy-light);">
                <div style="font-size:10px;font-weight:700;color:var(--navy);
                            text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px;">
                    Barang yang Diklaim
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <div style="width:48px;height:48px;background:var(--bg2);border-radius:var(--r);
                                border:0.5px solid var(--border);display:flex;align-items:center;
                                justify-content:center;flex-shrink:0;overflow:hidden;">
                        @if($report->foto_barang)
                            <img src="{{ asset('storage/'.$report->foto_barang) }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:var(--navy-mid);fill:none;stroke-width:1.8;">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--text);">
                            {{ $report->nama_barang }}
                        </div>
                        <div style="font-size:11px;color:var(--text-2);margin-top:2px;">
                            {{ $report->category->nama_category }} · 📍 {{ $report->lokasi }}
                        </div>
                        <div style="font-size:10px;color:var(--text-3);margin-top:2px;">
                            Ditemukan {{ $report->tanggal_kejadian->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Klaim --}}
            <div class="findit-card p-4 mb-3">
                <div class="page-title mb-1" style="font-size:16px;">Ajukan Klaim</div>
                <div style="font-size:11px;color:var(--text-2);margin-bottom:20px;">
                    Jelaskan secara detail mengapa barang ini milikmu. 
                    Informasi yang lengkap akan mempercepat proses verifikasi admin.
                </div>

                <form method="POST" action="{{ route('claims.store', $report->id) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Pesan Klaim</label>
                        <textarea name="pesan_klaim" rows="5"
                                  class="form-control @error('pesan_klaim') is-invalid @enderror"
                                  placeholder="Contoh: Ini dompet saya yang hilang pada tanggal 20 April. Isinya ada KTM atas nama saya, SIM, dan uang tunai. Dompetnya berwarna coklat tua dengan logo brand X di bagian depan...">{{ old('pesan_klaim') }}</textarea>
                        @error('pesan_klaim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div style="font-size:10px;color:var(--text-3);margin-top:4px;">
                            Minimal 20 karakter. Semakin detail semakin baik.
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div style="background:var(--accent-light);border-radius:var(--r);
                                padding:12px 14px;margin-bottom:20px;">
                        <div style="font-size:11px;font-weight:700;color:var(--warning);margin-bottom:6px;">
                            💡 Tips Pengajuan Klaim
                        </div>
                        <ul style="font-size:11px;color:var(--text-2);margin:0;padding-left:16px;line-height:1.7;">
                            <li>Sebutkan ciri khusus barang yang hanya kamu tahu</li>
                            <li>Jelaskan kapan dan di mana terakhir kamu melihat barang</li>
                            <li>Cantumkan isi barang jika relevan (dompet, tas, dll)</li>
                            <li>Admin akan menghubungi kamu setelah verifikasi</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-navy px-5 py-2">
                            Ajukan Klaim
                        </button>
                        <a href="{{ route('reports.show', $report->id) }}"
                           class="btn btn-outline-findit px-4 py-2">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>

</x-app-layout>