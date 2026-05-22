<x-admin-layout>
@section('title', 'Edit Kategori')

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.categories.index') }}"
                   style="color:var(--text-3);text-decoration:none;font-size:12px;">← Kembali</a>
                <div class="page-title" style="font-size:16px;">Edit Kategori</div>
            </div>

            <div class="findit-card p-4">
                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_category"
                               class="form-control @error('nama_category') is-invalid @enderror"
                               value="{{ old('nama_category', $category->nama_category) }}">
                        @error('nama_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-navy px-5 py-2">Simpan</button>
                        <a href="{{ route('admin.categories.index') }}"
                           class="btn btn-outline-findit px-4 py-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-admin-layout>