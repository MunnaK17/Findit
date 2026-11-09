<x-app-layout>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="page-title">Beri Testimoni</div>
        <div class="page-sub">Bagikan pengalaman Anda menggunakan FindIt</div>
    </div>
    <a href="{{ route('my.claims') }}" class="btn btn-outline-findit px-4">
        Kembali
    </a>
</div>

<div class="findit-card p-4">
    {{-- Info Klaim --}}
    <div class="alert alert-info mb-4">
        <strong>Klaim:</strong> {{ $claim->report->nama_barang }}<br>
        <strong>Lokasi:</strong> {{ $claim->report->lokasi }}<br>
        <strong>Status:</strong>
        <span class="badge bg-success">Disetujui</span>
    </div>

<form action="{{ route('testimonials.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_claim" value="{{ $claim->id }}">
        <input type="hidden" name="id_report" value="{{ $claim->id_report }}">

        {{-- Rating Bintang --}}
        <div class="mb-4">
            <label class="form-label fw-bold d-block">
                Rating <span class="text-danger">*</span>
            </label>
            <div class="d-flex gap-2 align-items-center">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button"
                            class="btn btn-sm p-2 star-btn"
                            data-rating="{{ $i }}"
                            style="background: transparent; border: none; padding: 4px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#dee2e6" stroke-width="1.5" class="star-icon">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </button>
                @endfor
                <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating') }}">
            </div>
            @error('rating')
                <div class="text-danger mt-2" style="font-size:13px;">{{ $message }}</div>
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
            >{{ old('isi_testimoni') }}</textarea>
            <div class="d-flex justify-content-end mt-1">
                <small class="text-muted"><span id="charCount">0</span>/500 karakter</small>
            </div>
            @error('isi_testimoni')
                <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-navy">
                <i class="bi bi-send me-2"></i>Kirim Testimoni
            </button>
            <a href="{{ route('my.claims') }}" class="btn btn-outline-findit">
                Batal
            </a>
        </div>
    </form>
</div>

<style>
    .star-btn {
        cursor: pointer;
        transition: transform 0.15s ease;
    }

    .star-btn:hover {
        transform: scale(1.2);
    }

    .star-btn.active .star-icon {
        fill: #ffc107;
        stroke: #ffc107;
    }

    .star-btn:focus {
        outline: 2px solid var(--navy);
        outline-offset: 2px;
        border-radius: 4px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const starBtns = document.querySelectorAll('.star-btn');
    const ratingValue = document.getElementById('ratingValue');
    const charCount = document.getElementById('charCount');
    const textarea = document.getElementById('isi_testimoni');

    // Initialize from old value
    const initialRating = parseInt(ratingValue.value) || 0;
    if (initialRating > 0) {
        setRating(initialRating);
    }

    // Add click handlers to each star button
    starBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingValue.value = rating;
            setRating(rating);
        });
    });

    function setRating(rating) {
        starBtns.forEach(function(btn) {
            const btnRating = parseInt(btn.dataset.rating);
            const icon = btn.querySelector('.star-icon');
            if (btnRating <= rating) {
                btn.classList.add('active');
                icon.style.fill = '#ffc107';
                icon.style.stroke = '#ffc107';
            } else {
                btn.classList.remove('active');
                icon.style.fill = 'none';
                icon.style.stroke = '#dee2e6';
            }
        });
    }

    // Character counter
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    charCount.textContent = textarea.value.length;
});
</script>

</x-app-layout>