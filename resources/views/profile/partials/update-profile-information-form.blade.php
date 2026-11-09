<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Perbarui nama, nomor telepon, cabang kampus, dan pengaturan notifikasi.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            {{-- Email login dikelola Supabase Auth dan menjadi tujuan email notifikasi, jadi tidak bisa diubah di sini. --}}
            <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-100" :value="$user->email" readonly aria-describedby="email-help" />
            <p id="email-help" class="mt-1 text-sm text-gray-600">Email login tidak bisa diubah. Notifikasi email dikirim ke alamat ini.</p>
        </div>

        <div>
            <x-input-label for="phone" value="Nomor Telepon (opsional)" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" placeholder="081234567890" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        @php
            $campuses = \App\Models\Campus::where('is_active', true)->orderBy('nama_kampus')->get();
        @endphp
        <div>
            <x-input-label for="id_campus" :value="__('Cabang Kampus UBSI')" />
            <select id="id_campus" name="id_campus" class="form-select mt-1 block w-full">
                <option value="">-- Pilih Cabang Kampus --</option>
                @foreach($campuses as $c)
                    <option value="{{ $c->id }}" {{ old('id_campus', $user->id_campus) == $c->id ? 'selected' : '' }}>
                        {{ $c->nama_kampus }} ({{ $c->kota }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('id_campus')" />
        </div>

        <div>
            <label for="email_notifications" class="inline-flex items-start gap-3 cursor-pointer">
                <input type="hidden" name="email_notifications" value="0">
                <input id="email_notifications" name="email_notifications" type="checkbox" value="1"
                       class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                       {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-900">Notifikasi Email</span>
                    <span class="block text-sm text-gray-600">Kirim email saat laporan atau klaim saya disetujui, ditolak, atau selesai. Pengaturan ini sama dengan di aplikasi mobile.</span>
                </span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
