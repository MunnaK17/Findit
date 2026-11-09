<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class WhatsAppService
{
    private ?string $provider;

    public function __construct()
    {
        $this->provider = config('services.wa.provider', 'twilio');
    }

    public function send(string $target, string $message): bool
    {
        $normalizedTarget = $this->normalizeNumber($target);

        Log::info('WhatsApp: Memulai pengiriman.', [
            'provider' => $this->provider,
            'target' => $normalizedTarget,
            'message_preview' => substr($message, 0, 50),
        ]);

        if ($this->provider === 'mock') {
            return $this->sendMock($normalizedTarget, $message);
        }

        if ($this->provider === 'twilio') {
            return $this->sendTwilio($normalizedTarget, $message);
        }

        if ($this->provider === 'ultramsg') {
            return $this->sendUltramsg($normalizedTarget, $message);
        }

        Log::warning('WhatsApp: Provider tidak dikenali.', ['provider' => $this->provider]);
        return false;
    }

    private function sendMock(string $target, string $message): bool
    {
        Log::info('WhatsApp [MOCK]: Pesan di-queue', [
            'target' => $target,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
        ]);

        return true;
    }

    private function sendTwilio(string $target, string $message): bool
    {
        try {
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $from = config('services.twilio.whatsapp_from');

            if (!$accountSid || !$authToken) {
                Log::warning('WhatsApp: Konfigurasi Twilio belum lengkap.');
                return false;
            }

            $client = new Client($accountSid, $authToken);

            $message = $client->messages->create(
                "whatsapp:{$target}",
                [
                    'from' => $from,
                    'body' => $message,
                ]
            );

            Log::info('WhatsApp [Twilio]: Terkirim', [
                'target' => $target,
                'message_sid' => $message->sid,
                'status' => $message->status,
            ]);

            return true;
        } catch (TwilioException $e) {
            Log::error('WhatsApp [Twilio] gagal: ' . $e->getMessage(), [
                'target' => $target,
                'code' => $e->getCode(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp [Twilio] exception: ' . $e->getMessage(), [
                'target' => $target,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    private function sendUltramsg(string $target, string $message): bool
    {
        $apiUrl = rtrim(config('services.wa.api_url', ''), '/');
        $instanceId = config('services.wa.instance_id');
        $token = config('services.wa.token');

        if (empty($token) || empty($instanceId)) {
            Log::warning('WhatsApp: Konfigurasi Ultramsg belum lengkap.');
            return false;
        }

        try {
            $endpoint = "{$apiUrl}/messages/chat";

            $response = Http::asForm()
                ->withHeaders(['Authorization' => 'Bearer ' . $token])
                ->post($endpoint, [
                    'token' => $token,
                    'to' => $target,
                    'body' => $message,
                    'priority' => 1,
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['sent'] ?? '') === 'true') {
                Log::info('WhatsApp [Ultramsg]: Terkirim', [
                    'target' => $target,
                    'message_id' => $body['id'] ?? null,
                ]);
                return true;
            }

            Log::error('WhatsApp [Ultramsg] gagal', [
                'target' => $target,
                'status' => $response->status(),
                'response' => $body ?? $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp [Ultramsg] exception: ' . $e->getMessage(), [
                'target' => $target,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    private function normalizeNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        if (!str_starts_with($number, '62')) {
            return '62' . $number;
        }

        return $number;
    }
}
