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
     * Ambil (atau buat bila belum ada) profil lokal untuk user Supabase.
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
            'role'       => $meta['role'] ?? 'mahasiswa',
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
