<x-app-layout>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Klaim Saya</div>
            <div class="page-sub">Status pengajuan klaim barang temuan</div>
        </div>
        <a href="{{ route('reports.temuan') }}"
           class="btn btn-outline-findit px-4">
            Cari Barang Temuan
        </a>
    </div>

    @if($claims->count() > 0)
        <div class="d-flex flex-column gap-3">
            @foreach($claims as $claim)
                <div class="findit-card p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3">

                        {{-- Info Barang --}}
                        <div class="d-flex gap-3 align-items-start flex-grow-1">
                            <div style="width:48px;height:48px;background:var(--navy-pale);border-radius:var(--r);
                                        border:0.5px solid var(--border);display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0;overflow:hidden;">
                                @if($claim->report->foto_barang)
                                    <img src="{{ asset('storage/'.$claim->report->foto_barang) }}"
                                         style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:var(--navy-mid);fill:none;stroke-width:1.8;">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--text);">
                                    {{ $claim->report->nama_barang }}
                                </div>
                                <div style="font-size:11px;color:var(--text-2);margin-top:2px;">
                                    {{ $claim->report->category->nama_category }} ·
                                    📍 {{ $claim->report->lokasi }}
                                </div>
                                <div style="font-size:10px;color:var(--text-3);margin-top:6px;
                                            background:var(--bg);border-radius:6px;padding:6px 10px;
                                            border:0.5px solid var(--border);">
                                    <span style="font-weight:600;color:var(--text-2);">Pesan klaim:</span>
                                    {{ Str::limit($claim->pesan_klaim, 100) }}
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="text-end" style="flex-shrink:0;">
                            @if($claim->status_klaim === 'pending')
                                <span class="findit-badge b-amber">Pending</span>
                            @elseif($claim->status_klaim === 'approved')
                                <span class="findit-badge b-green">Disetujui</span>
                            @else
                                <span class="findit-badge b-red">Ditolak</span>
                            @endif
                            <div style="font-size:10px;color:var(--text-3);margin-top:6px;">
                                {{ $claim->tanggal_klaim instanceof \Carbon\Carbon
                                    ? $claim->tanggal_klaim->format('d M Y')
                                    : \Carbon\Carbon::parse($claim->tanggal_klaim)->format('d M Y') }}
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('reports.show', $claim->id_report) }}"
                                   style="font-size:11px;color:var(--navy);font-weight:600;text-decoration:none;">
                                    Lihat Barang →
                                </a>
                            </div>
                        </div>

                    </div>

                    {{-- Info jika approved --}}
                    @if($claim->status_klaim === 'approved')
                        <div style="margin-top:12px;background:var(--success-light);border-radius:var(--r);
                                    padding:10px 14px;border:0.5px solid rgba(26,138,90,0.2);">
                            <div style="font-size:11px;font-weight:700;color:var(--success);margin-bottom:2px;">
                                ✅ Klaim Disetujui!
                            </div>
                            <div style="font-size:11px;color:var(--text-2);">
                                Silakan datang ke admin kampus di lobby untuk pengambilan barang. Tunjukkan halaman ini sebagai bukti.
                            </div>
                        </div>

                        {{-- Tombol Testimoni --}}
                        @if($claim->hasTestimonial())
                            <div style="margin-top:12px;background:var(--navy-pale);border-radius:var(--r);
                                        padding:10px 14px;border:0.5px solid rgba(26,58,107,0.15);">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="color:#ffc107;">★★★★★</span>
                                    <span style="font-size:11px;color:var(--text-2);">
                                        Terima kasih sudah memberikan testimoni!
                                    </span>
                                </div>
                            </div>
                        @else
                            <div style="margin-top:12px;display:flex;align-items:center;gap:8px;">
                                <a href="{{ route('testimonials.create', $claim->id) }}"
                                   class="btn btn-sm"
                                   style="background:var(--accent);color:var(--navy);font-weight:700;font-size:12px;border-radius:var(--r);padding:8px 16px;text-decoration:none;">
                                    <i class="bi bi-chat-quote me-1"></i>
                                    Beri Testimoni
                                </a>
                            </div>
                        @endif
                    @elseif($claim->status_klaim === 'rejected')
                        <div style="margin-top:12px;background:var(--danger-light);border-radius:var(--r);
                                    padding:10px 14px;border:0.5px solid rgba(220,38,38,0.2);">
                            <div style="font-size:11px;font-weight:700;color:var(--danger);margin-bottom:2px;">
                                ❌ Klaim Ditolak
                            </div>
                            <div style="font-size:11px;color:var(--text-2);">
                                Klaim kamu tidak disetujui oleh admin. Jika ada kekeliruan, hubungi admin kampus.
                            </div>
                        </div>
                    @else
                        <div style="margin-top:12px;background:var(--warning-light);border-radius:var(--r);
                                    padding:10px 14px;border:0.5px solid rgba(217,119,6,0.2);">
                            <div style="font-size:11px;color:var(--warning);">
                                ⏳ Klaim sedang ditinjau oleh admin. Harap tunggu konfirmasi.
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $claims->links() }}
        </div>
    @else
        <div class="findit-card p-5 text-center">
            <div style="font-size:32px;margin-bottom:12px;">🔖</div>
            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
                Belum ada klaim
            </div>
            <div style="font-size:12px;color:var(--text-2);margin-bottom:16px;">
                Kamu belum pernah mengajukan klaim barang temuan
            </div>
            <a href="{{ route('reports.temuan') }}" class="btn btn-navy px-4">
                Lihat Barang Temuan
            </a>
        </div>
    @endif

</x-app-layout>