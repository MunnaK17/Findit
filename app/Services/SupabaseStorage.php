<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Foto laporan di Supabase Storage bucket `reports`, sama dengan aplikasi mobile.
 *
 * Kredensial: SUPABASE_SERVICE_ROLE_KEY bila diisi di .env (server-side), selain itu
 * token sesi pengguna yang login. Policy bucket hanya menerima file bernama
 * reports/<millis>_<user_id>.<ext> dari pengguna itu sendiri.
 */
class SupabaseStorage
{
    private const BUCKET = 'reports';

    public function __construct(private SupabaseAuth $auth)
    {
    }

    private function baseUrl(): string
    {
        return rtrim(config('services.supabase.url'), '/');
    }

    /** Header otorisasi, atau null bila tidak ada kredensial yang bisa dipakai. */
    private function headers(): ?array
    {
        $anon = config('services.supabase.anon_key');
        $service = config('services.supabase.service_role_key');
        if ($service) {
            return ['apikey' => $service, 'Authorization' => 'Bearer ' . $service];
        }

        $token = $this->auth->accessToken();
        return $token ? ['apikey' => $anon, 'Authorization' => 'Bearer ' . $token] : null;
    }

    /** Unggah foto dan kembalikan URL publik, atau null bila gagal. */
    public function uploadReportPhoto(UploadedFile $file, string $userId): ?string
    {
        $headers = $this->headers();
        if (!$headers) {
            Log::warning('Upload foto dibatalkan: tidak ada sesi Supabase atau service key.');
            return null;
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'jpg';
        }
        $objectPath = 'reports/' . (int) round(microtime(true) * 1000) . '_' . $userId . '.' . $ext;

        try {
            $res = Http::withHeaders($headers)
                ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'image/jpeg')
                ->timeout(30)
                ->post($this->baseUrl() . '/storage/v1/object/' . self::BUCKET . '/' . $objectPath);
        } catch (\Throwable $e) {
            Log::warning('Upload foto ke Supabase gagal: ' . $e->getMessage());
            return null;
        }

        if (!$res->successful()) {
            Log::warning('Upload foto ke Supabase ditolak', ['status' => $res->status(), 'body' => $res->json()]);
            return null;
        }

        return $this->baseUrl() . '/storage/v1/object/public/' . self::BUCKET . '/' . $objectPath;
    }

    /**
     * Hapus foto lama (best effort). Dengan token pengguna hanya file miliknya yang terhapus;
     * dengan service key semua file bisa dihapus.
     */
    public function deleteByUrl(?string $url): void
    {
        $marker = '/storage/v1/object/public/' . self::BUCKET . '/';
        if (!$url || !str_contains($url, $marker)) {
            return;
        }

        $headers = $this->headers();
        if (!$headers) {
            return;
        }

        $path = substr($url, strpos($url, $marker) + strlen($marker));
        try {
            Http::withHeaders($headers)
                ->timeout(15)
                ->delete($this->baseUrl() . '/storage/v1/object/' . self::BUCKET, ['prefixes' => [$path]]);
        } catch (\Throwable $e) {
            Log::info('Hapus foto lama gagal: ' . $e->getMessage());
        }
    }
}
