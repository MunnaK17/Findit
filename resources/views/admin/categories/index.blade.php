<x-admin-layout>
@section('title', 'Kelola Kategori')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Kelola Kategori</div>
            <div class="page-sub">Manajemen kategori barang</div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Form Tambah --}}
        <div class="col-md-4">
            <div class="findit-card p-4">
                <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:16px;">
                    Tambah Kategori Baru
                </div>
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_category"
                               class="form-control @error('nama_category') is-invalid @enderror"
                               value="{{ old('nama_category') }}"
                               placeholder="Contoh: Elektronik">
                        @error('nama_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioritas</label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                            <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>
                                🔴 Critical - Barang sangat penting
                            </option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                🟡 High - Barang cukup penting
                            </option>
                            <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>
                                🟢 Normal - Barang biasa
                            </option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-navy w-100 py-2">
                        + Tambah Kategori
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Kategori --}}
        <div class="col-md-8">
            <div class="table-card">
                <div style="padding:12px 16px;border-bottom:0.5px solid var(--border);">
                    <div style="font-size:13px;font-weight:700;color:var(--text);">
                        Daftar Kategori ({{ $categories->total() }})
                    </div>
                </div>
                <div class="table-responsive-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th>Prioritas</th>
                                <th>Jumlah Laporan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td style="color:var(--text-3);">{{ $category->id }}</td>
                                    <td style="font-size:12px;font-weight:600;">
                                        {{ $category->nama_category }}
                                    </td>
                                    <td>
                                        {!! $category->priorityBadge() !!}
                                    </td>
                                    <td>
                                        <span class="findit-badge b-navy">
                                            {{ $category->reports_count }} laporan
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                               style="font-size:11px;color:var(--warning);font-weight:600;text-decoration:none;">
                                                Edit
                                            </a>
                                            @if($category->reports_count === 0)
                                                <span style="color:var(--border);">|</span>
                                                <form method="POST"
                                                      action="{{ route('admin.categories.destroy', $category->id) }}"
                                                      onsubmit="return confirm('Hapus kategori ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            style="background:none;border:none;font-size:11px;color:var(--danger);font-weight:600;cursor:pointer;padding:0;">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--text-3);padding:32px;">
                                        Belum ada kategori
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

</x-admin-layout>