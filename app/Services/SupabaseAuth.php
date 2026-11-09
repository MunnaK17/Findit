<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Klien tipis Supabase Auth (GoTrue) lewat REST API.
 * Akun & kode OTP email dikelola Supabase, sama dengan aplikasi mobile.
 */
class SupabaseAuth
{
    private const SESSION_KEY = 'supabase_session';

    private function base(): string
    {
        return rtrim(config('services.supabase.url'), '/') . '/auth/v1';
    }

    private function http()
    {
        $key = config('services.supabase.anon_key');

        return Http::withHeaders(['apikey' => $key, 'Authorization' => 'Bearer ' . $key])
            ->acceptJson()
            ->timeout(20);
    }

    /** Pastikan hanya email resmi BSI yang diterima. */
    public static function isBsiEmail(string $email): bool
    {
        return (bool) preg_match('/^[^@\s]+@([a-z0-9-]+\.)*bsi\.ac\.id$/i', trim($email));
    }

    public function signUp(string $email, string $password, array $meta): array
    {
        $res = $this->http()->post($this->base() . '/signup', [
            'email'    => strtolower(trim($email)),
            'password' => $password,
            'data'     => $meta,
        ]);

        return $this->result($res);
    }

    public function signIn(string $email, string $password): array
    {
        $res = $this->http()->post($this->base() . '/token?grant_type=password', [
            'email'    => strtolower(trim($email)),
            'password' => $password,
        ]);

        return $this->result($res);
    }

    public function verifyOtp(string $email, string $token): array
    {
        $res = $this->http()->post($this->base() . '/verify', [
            'type'  => 'signup',
            'email' => strtolower(trim($email)),
            'token' => trim($token),
        ]);

        return $this->result($res);
    }

    public function resend(string $email): array
    {
        $res = $this->http()->post($this->base() . '/resend', [
            'type'  => 'signup',
            'email' => strtolower(trim($email)),
        ]);

        return $this->result($res);
    }

    /**
     * Simpan token sesi Supabase pengguna di session Laravel. Dipakai untuk memanggil
     * Supabase (mis. Storage) atas nama pengguna, sehingga policy RLS tetap berlaku.
     */
    public function rememberSession(array $data): void
    {
        if (empty($data['access_token'])) {
            return;
        }

        session([self::SESSION_KEY => [
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_at'    => (int) ($data['expires_at'] ?? (time() + (int) ($data['expires_in'] ?? 3600))),
        ]]);
    }

    public function forgetSession(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /** Access token pengguna yang login, diperbarui otomatis bila hampir kedaluwarsa. */
    public function accessToken(): ?string
    {
        $session = session(self::SESSION_KEY);
        if (!$session) {
            return null;
        }

        if ($session['expires_at'] > time() + 60) {
            return $session['access_token'];
        }

        if (empty($session['refresh_token'])) {
            return null;
        }

        $res = $this->http()->post($this->base() . '/token?grant_type=refresh_token', [
            'refresh_token' => $session['refresh_token'],
        ]);
        $result = $this->result($res);
        if (!$result['ok']) {
            $this->forgetSession();
            return null;
        }

        $this->rememberSession($result['data']);

        return $result['data']['access_token'];
    }

    /**
     * Hapus akun Supabase Auth (profil, laporan, dan klaim ikut lewat foreign key).
     * Memerlukan SUPABASE_SERVICE_ROLE_KEY di .env.
     */
    public function deleteUser(string $userId): array
    {
        $serviceKey = config('services.supabase.service_role_key');
        if (!$serviceKey) {
            return [
                'ok'      => false,
                'message' => 'SUPABASE_SERVICE_ROLE_KEY belum diisi di .env, akun tidak bisa dihapus dari web.',
            ];
        }

        $res = Http::withHeaders(['apikey' => $serviceKey, 'Authorization' => 'Bearer ' . $serviceKey])
            ->acceptJson()
            ->timeout(20)
            ->delete($this->base() . '/admin/users/' . urlencode($userId));

        return $this->result($res);
    }

    /**
     * Ambil (atau buat bila belum ada) profil lokal untuk user Supabase.
     * Profil biasanya sudah dibuat trigger handle_new_user saat pendaftaran.
     */
    public function profileFor(array $authUser): User
    {
        $meta = $authUser['user_metadata'] ?? [];

        $user = User::find($authUser['id']);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->forceFill([
            'id'         => $authUser['id'],
            'name'       => $meta['name'] ?? explode('@', $authUser['email'])[0],
            'email'      => $authUser['email'],
            // user_metadata diisi sendiri oleh pendaftar: role admin tidak pernah diambil dari sini.
            'role'       => ($meta['role'] ?? null) === 'staff' ? 'staff' : 'mahasiswa',
            'nim'        => $meta['nim'] ?? null,
            'nip'        => $meta['nip'] ?? null,
            'phone'      => $meta['phone'] ?? null,
            'id_campus'  => $meta['id_campus'] ?? null,
            'prodi_unit' => $meta['prodi_unit'] ?? null,
        ])->save();

        return $user;
    }

    private function result($res): array
    {
        $json = $res->json() ?? [];

        if ($res->successful()) {
            return ['ok' => true, 'data' => $json];
        }

        Log::warning('Supabase auth error', ['status' => $res->status(), 'body' => $json]);

        return [
            'ok'      => false,
            'status'  => $res->status(),
            'code'    => $json['error_code'] ?? $json['code'] ?? null,
            'message' => $json['msg'] ?? $json['error_description'] ?? $json['message'] ?? 'Terjadi kesalahan pada layanan autentikasi.',
        ];
    }
}
