<x-guest-layout>

    <div style="font-size:25px;font-weight:800;color:var(--text);margin-bottom:5px;">
        Buat Akun Baru
    </div>
    <div style="font-size:14px;color:var(--text-2);margin-bottom:24px;">
        Khusus sivitas UBSI. Gunakan email resmi <strong>@bsi.ac.id</strong>.
    </div>

    @php
        $oldRole = old('role', 'mahasiswa');
        $oldProdi = old('prodi_unit');
    @endphp

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- Role --}}
        <div class="mb-3">
            <label class="form-label">Daftar sebagai</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="role" id="roleMhs" value="mahasiswa" {{ $oldRole === 'mahasiswa' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="roleMhs">Mahasiswa</label>
                <input type="radio" class="btn-check" name="role" id="roleStaff" value="staff" {{ $oldRole === 'staff' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="roleStaff">Staff / Dosen</label>
            </div>
            @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Cabang kampus --}}
        <div class="mb-3">
            <label class="form-label">Cabang Kampus Asal</label>
            <select name="id_campus" class="form-select @error('id_campus') is-invalid @enderror">
                <option value="">-- Pilih cabang kampus --</option>
                @foreach ($campuses as $c)
                    <option value="{{ $c->id }}" {{ old('id_campus') == $c->id ? 'selected' : '' }}>
                        {{ $c->nama_kampus }} ({{ $c->kota }})
                    </option>
                @endforeach
            </select>
            @error('id_campus') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="Nama sesuai KTM / identitas" autofocus>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email BSI</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="nama@bsi.ac.id">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- NIM (mahasiswa) --}}
        <div class="mb-3" id="wrapNim">
            <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
            <input type="text" name="nim"
                   class="form-control @error('nim') is-invalid @enderror"
                   value="{{ old('nim') }}" placeholder="Misal: 19241001">
            @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- NIP (staff) --}}
        <div class="mb-3" id="wrapNip" style="display:none;">
            <label class="form-label">NIP / NIDN</label>
            <input type="text" name="nip"
                   class="form-control @error('nip') is-invalid @enderror"
                   value="{{ old('nip') }}" placeholder="Misal: 0412345678">
            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Prodi / unit kerja --}}
        <div class="mb-3">
            <label class="form-label" id="prodiLabel">Program Studi</label>
            <select name="prodi_unit" id="prodiSelect" class="form-select @error('prodi_unit') is-invalid @enderror"></select>
            @error('prodi_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nomor HP / WhatsApp</label>
            <input type="tel" name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}" placeholder="081234567890">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Min. 8 karakter">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
        </div>

        {{-- Captcha --}}
        <div class="mb-4">
            <label class="form-label">Verifikasi</label>
            <div style="background:#f0f4f8;padding:12px 15px;border-radius:8px;margin-bottom:10px;display:flex;align-items:center;gap:10px;">
                <span style="font-size:18px;font-weight:700;color:var(--navy);">
                    {{ $captcha_question ?? session('captcha_question') ?? 'Refresh halaman untuk muat captcha' }}
                </span>
            </div>
            <input type="number" name="captcha_answer"
                   class="form-control @error('captcha_answer') is-invalid @enderror"
                   placeholder="Ketik jawaban di sini" min="0" max="18" style="max-width:200px;">
            @error('captcha_answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-navy w-100 py-2" style="font-size:16px;">
            Daftar Sekarang
        </button>

        <div style="text-align:center;margin-top:20px;font-size:14px;color:var(--text-2);">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="color:var(--navy);font-weight:700;text-decoration:none;">
                Masuk di sini
            </a>
        </div>
    </form>

    <script>
        (function () {
            const prodi = {
                mahasiswa: @json($prodiMahasiswa),
                staff: @json($prodiStaff)
            };
            const selected = @json($oldProdi);
            const form = document.getElementById('registerForm');
            const select = document.getElementById('prodiSelect');

            function currentRole() {
                return form.querySelector('input[name=role]:checked').value;
            }

            function render() {
                const role = currentRole();
                document.getElementById('wrapNim').style.display = role === 'mahasiswa' ? '' : 'none';
                document.getElementById('wrapNip').style.display = role === 'staff' ? '' : 'none';
                document.getElementById('prodiLabel').textContent = role === 'mahasiswa' ? 'Program Studi' : 'Unit Kerja / Prodi';

                select.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = role === 'mahasiswa' ? '-- Pilih program studi --' : '-- Pilih unit kerja / prodi --';
                select.appendChild(placeholder);

                Object.entries(prodi[role]).forEach(([group, items]) => {
                    const og = document.createElement('optgroup');
                    og.label = group;
                    items.forEach(item => {
                        const o = document.createElement('option');
                        o.value = item;
                        o.textContent = item;
                        if (item === selected) o.selected = true;
                        og.appendChild(o);
                    });
                    select.appendChild(og);
                });
            }

            form.querySelectorAll('input[name=role]').forEach(r => r.addEventListener('change', () => {
                render();
            }));
            render();
        })();
    </script>

</x-guest-layout>
