<x-app-layout>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="page-title">Edit Testimoni</div>
        <div class="page-sub">Perbarui testimoni Anda</div>
    </div>
    <a href="{{ route('my.testimonials') }}" class="btn btn-outline-findit px-4">
        Kembali
    </a>
</div>

<div class="findit-card p-4">
    {{-- Info Klaim --}}
    <div class="alert alert-info mb-4">
        <strong>Barang:</strong> {{ $testimonial->report->nama_barang }}<br>
        <small class="text-muted">Tanggal testimoni: {{ $testimonial->created_at->format('d M Y') }}</small>
    </div>

    <form action="{{ route('testimonials.update', $testimonial->id) }}" method="POST" id="testimonialForm">
        @csrf
        @method('PUT')

        {{-- Rating Bintang --}}
        <div class="mb-4">
            <label class="form-label fw-bold d-block">
                Rating <span class="text-danger">*</span>
            </label>
            <div class="rating-wrapper d-inline-flex gap-1" role="radiogroup" aria-label="Pilih rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star-btn {{ $i <= $testimonial->rating ? 'active' : '' }}"
                          data-value="{{ $i }}"
                          role="radio"
                          aria-checked="{{ $i <= $testimonial->rating ? 'true' : 'false' }}"
                          tabindex="0">
                        <svg width="40" height="40" viewBox="0 0 24 24"
                             fill="{{ $i <= $testimonial->rating ? '#ffc107' : 'none' }}"
                             stroke="currentColor"
                             stroke-width="1.5"
                             style="color: {{ $i <= $testimonial->rating ? '#ffc107' : '#dee2e6' }}">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating', $testimonial->rating) }}">
            <div class="rating-error text-danger mt-1" style="display:none;font-size:13px;">Silakan pilih rating bintang</div>
            @error('rating')
                <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Testimoni --}}
        <div class="mb-3">
            <label for="isi_testimoni" class="form-label fw-bold">
                Testimoni <span class="text-danger">*</span>
            </label>
            <textarea
                class="form-control @error('isi_testimoni') is-invalid @enderror"
                id="isi_testimoni"
                name="isi_testimoni"
                rows="5"
                placeholder="Ceritakan pengalaman Anda menggunakan FindIt..."
                required
            >{{ old('isi_testimoni', $testimonial->isi_testimoni) }}</textarea>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">Minimal 20 karakter</small>
                <small class="text-muted"><span id="charCount">{{ strlen($testimonial->isi_testimoni) }}</span>/500</small>
            </div>
            @error('isi_testimoni')
                <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-navy">
                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('my.testimonials') }}" class="btn btn-outline-findit">
                Batal
            </a>
        </div>
    </form>
</div>

<style>
    .rating-wrapper {
        padding: 8px 0;
    }

    .star-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #dee2e6;
        transition: all 0.2s ease;
        border-radius: 8px;
        padding: 4px;
    }

    .star-btn:hover {
        transform: scale(1.15);
    }

    .star-btn:hover svg {
        color: #ffc107 !important;
    }

    .star-btn.active svg {
        color: #ffc107;
    }

    .star-btn:focus {
        outline: 2px solid var(--navy);
        outline-offset: 2px;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starBtns = document.querySelectorAll('.star-btn');
    const ratingValue = document.getElementById('ratingValue');
    const charCount = document.getElementById('charCount');
    const textarea = document.getElementById('isi_testimoni');
    const form = document.getElementById('testimonialForm');
    const ratingError = document.querySelector('.rating-error');

    // Star click handlers
    starBtns.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            ratingValue.value = value;
            setRating(value);
            ratingError.style.display = 'none';
        });

        star.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const value = parseInt(this.dataset.value);
                ratingValue.value = value;
                setRating(value);
                ratingError.style.display = 'none';
            }
        });
    });

    function setRating(rating) {
        starBtns.forEach((star, index) => {
            const starValue = index + 1;
            const svg = star.querySelector('svg');
            if (starValue <= rating) {
                star.classList.add('active');
                svg.style.fill = '#ffc107';
                svg.style.color = '#ffc107';
                star.setAttribute('aria-checked', 'true');
            } else {
                star.classList.remove('active');
                svg.style.fill = 'none';
                svg.style.color = '#dee2e6';
                star.setAttribute('aria-checked', 'false');
            }
        });
    }

    // Character counter
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Form validation on submit
    form.addEventListener('submit', function(e) {
        const rating = parseInt(ratingValue.value) || 0;
        if (rating === 0) {
            e.preventDefault();
            ratingError.style.display = 'block';
            starBtns[0].focus();
        }

        const testimonial = textarea.value.trim();
        if (testimonial.length < 20) {
            e.preventDefault();
            textarea.focus();
        }
    });
});
</script>
@endpush

</x-app-layout>