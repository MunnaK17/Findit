<x-admin-layout>
@section('title', 'Kelola User')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="page-title">Kelola User</div>
            <div class="page-sub">Daftar semua pengguna FindIt</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="findit-card mb-4" style="padding:14px 16px;">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="d-flex gap-2 flex-wrap">
                <div class="search-bar-wrap flex-grow-1">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama, email, NIM, atau nomor HP...">
                </div>
                <select name="role" class="form-select" style="width:auto;">
                    <option value="">Semua Role</option>
                    <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="admin"     {{ request('role') === 'admin'     ? 'selected' : '' }}>Admin</option>
                </select>
                <button type="submit" class="btn btn-navy px-3">Cari</button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-findit px-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>NIM</th>
                        <th>No. HP</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th>Laporan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="color:var(--text-3);">{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px;height:32px;border-radius:8px;background:var(--navy);
                                                color:#fff;font-size:11px;font-weight:800;
                                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:12px;">{{ $user->name }}</div>
                                        <div style="font-size:10px;color:var(--text-3);">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:12px;">{{ $user->nim ?? '-' }}</td>
                            <td style="font-size:12px;">{{ $user->phone ?? '-' }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="findit-badge b-navy">Admin</span>
                                @else
                                    <span class="findit-badge b-gray">Mahasiswa</span>
                                @endif
                            </td>
                            <td style="font-size:11px;color:var(--text-2);">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td style="font-size:12px;font-weight:600;">
                                {{ $user->reports()->count() }}
                            </td>
                            <td>
                                @if($user->id !== Auth::id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                          onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                style="background:none;border:none;font-size:11px;color:var(--danger);font-weight:600;cursor:pointer;padding:0;">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:11px;color:var(--text-3);">— Kamu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--text-3);padding:32px;">
                                Tidak ada user ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $users->withQueryString()->links() }}
    </div>

</x-admin-layout>
