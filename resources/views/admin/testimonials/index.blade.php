<x-admin-layout>
@section('title', 'Kelola Testimoni')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="page-title">Kelola Testimoni</div>
        <div class="page-sub">Pantau dan kelola testimoni dari pengguna</div>
    </div>
</div>

{{-- Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div style="font-size:12px;color:var(--text-3);font-weight:600;margin-bottom:8px;">Total Testimoni</div>
            <div style="font-size:28px;font-weight:800;color:var(--text);">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div style="font-size:12px;color:var(--text-3);font-weight:600;margin-bottom:8px;">Rating Rata-rata</div>
            <div style="font-size:28px;font-weight:800;color:var(--text);">
                {{ $stats['avg_rating'] }}
                <span style="color:#ffc107;font-size:16px;">★</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div style="font-size:12px;color:var(--text-3);font-weight:600;margin-bottom:8px;">Rating 5 Bintang</div>
            <div style="font-size:28px;font-weight:800;color:var(--success);">{{ $stats['rating_5'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div style="font-size:12px;color:var(--text-3);font-weight:600;margin-bottom:8px;">Rating 1 Bintang</div>
            <div style="font-size:28px;font-weight:800;color:var(--danger);">{{ $stats['rating_1'] }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="findit-card p-3 mb-4">
    <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:4px;">Cari</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Nama atau NIM user..."
                   value="{{ request('search') }}">
        </div>
        <div style="width:150px;">
            <label style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:4px;">Filter Rating</label>
            <select name="rating" class="form-select">
                <option value="">Semua</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                        {{ $i }} Bintang
                    </option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-navy">
            <i class="bi bi-search me-1"></i>Filter
        </button>
        @if(request()->has('search') || request()->has('rating'))
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-findit">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Tabel Testimoni --}}
<div class="table-card">
    <div style="padding:12px 16px;border-bottom:0.5px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:13px;font-weight:700;color:var(--text);">
            Daftar Testimoni ({{ $testimonials->total() }})
        </div>
    </div>
    <div class="table-responsive-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Barang</th>
                    <th>Rating</th>
                    <th>Testimoni</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $testimonial)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:36px;height:36px;background:var(--navy);color:var(--accent);font-size:11px;font-weight:bold;">
                                    {{ $testimonial->getUserInitials() }}
                                </div>
                                <div>
                                    <div style="font-size:12px;font-weight:600;">{{ $testimonial->user->name }}</div>
                                    <div style="font-size:10px;color:var(--text-3);">{{ $testimonial->user->nim }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:600;">{{ $testimonial->report->nama_barang }}</div>
                            <div style="font-size:10px;color:var(--text-3);">{{ $testimonial->report->lokasi }}</div>
                        </td>
                        <td>
                            <div>
                                @for ($i = 1; $i <= 5; $i++)
                                    <span style="color: {{ $i <= $testimonial->rating ? '#ffc107' : '#dee2e6' }}; font-size:12px;">
                                        @if($i <= $testimonial->rating) ★ @else ☆ @endif
                                    </span>
                                @endfor
                            </div>
                        </td>
                        <td>
                            <div style="max-width:250px;font-size:12px;color:var(--text-2);">
                                {{ Str::limit($testimonial->isi_testimoni, 80) }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:11px;color:var(--text-3);">
                                {{ $testimonial->created_at->format('d M Y') }}
                            </div>
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.testimonials.destroy', $testimonial->id) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus testimoni ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="background:none;border:none;font-size:11px;color:var(--danger);font-weight:600;cursor:pointer;padding:0;">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--text-3);padding:40px;">
                            <i class="bi bi-chat-quote fs-4 d-block mb-2"></i>
                            Belum ada testimoni
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $testimonials->links() }}
</div>

</x-admin-layout>