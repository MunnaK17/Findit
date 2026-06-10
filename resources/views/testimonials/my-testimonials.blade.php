<x-app-layout>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="page-title">Testimoni Saya</div>
        <div class="page-sub">Kelola testimoni yang telah Anda berikan</div>
    </div>
    <a href="{{ route('my.claims') }}" class="btn btn-outline-findit px-4">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if($testimonials->isEmpty())
    <div class="findit-card p-5 text-center">
        <div style="font-size:32px;margin-bottom:12px;">💬</div>
        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">
            Belum Ada Testimoni
        </div>
        <div style="font-size:12px;color:var(--text-2);margin-bottom:16px;">
            Anda belum memberikan testimoni apapun.
        </div>
        <a href="{{ route('my.claims') }}" class="btn btn-navy px-4">
            <i class="bi bi-list-check me-2"></i>Lihat Klaim Saya
        </a>
    </div>
@else
    <div class="row g-4">
        @foreach($testimonials as $testimonial)
            <div class="col-md-6 col-lg-4">
                <div class="findit-card p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--navy); color: var(--accent); font-size: 14px; font-weight: bold;">
                            {{ $testimonial->getUserInitials() }}
                        </div>
                        <div>
                            <strong class="d-block" style="font-size:14px;">{{ $testimonial->user->name }}</strong>
                            <small class="text-muted" style="font-size:11px;">{{ $testimonial->created_at->format('d M Y') }}</small>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <span style="color: {{ $i <= $testimonial->rating ? '#ffc107' : '#dee2e6' }}; font-size: 1.1rem;">
                                @if($i <= $testimonial->rating) ★ @else ☆ @endif
                            </span>
                        @endfor
                    </div>

                    {{-- Testimoni --}}
                    <p style="font-size:13px;color:var(--text-2);margin-bottom:0;">{{ $testimonial->isi_testimoni }}</p>

                    <div class="d-flex gap-2 mt-3 pt-3" style="border-top:0.5px solid var(--border);">
                        <a href="{{ route('testimonials.edit', $testimonial->id) }}" class="btn btn-outline-findit btn-sm flex-grow-1">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form action="{{ route('testimonials.destroy', $testimonial->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Yakin ingin menghapus testimoni ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $testimonials->links() }}
    </div>
@endif

</x-app-layout>