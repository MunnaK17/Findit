<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function __construct(private WhatsAppService $wa) {}

    public function send(object $notifiable, Notification $notification): void
    {
        Log::info('WhatsAppChannel: Memulai proses kirim.', [
            'notifiable_type' => $notifiable::class,
            'notifiable_id' => $notifiable->id ?? null,
            'notification' => $notification::class,
        ]);

        if (! method_exists($notification, 'toWhatsApp')) {
            Log::warning('WhatsAppChannel: Notification tidak punya method toWhatsApp.', [
                'notification' => $notification::class,
            ]);
            return;
        }

        $phone = $notifiable->phone ?? $notifiable->no_hp ?? null;

        if (! $phone) {
            Log::warning('WhatsAppChannel: Notifiable tidak punya nomor HP.', [
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => $notifiable->id ?? null,
                'available_fields' => array_keys(get_object_vars($notifiable)),
            ]);
            return;
        }

        Log::info('WhatsAppChannel: Nomor HP ditemukan.', [
            'phone' => $phone,
 'notifiable_id' => $notifiable->id ?? null,
        ]);

        $message = $notification->toWhatsApp($notifiable);
        $sent = $this->wa->send($phone, $message);

        if ($sent) {
            Log::info('WhatsAppChannel: Berhasil terkirim.', [
                'phone' => $phone,
                'notifiable_id' => $notifiable->id ?? null,
            ]);
        } else {
            Log::error('WhatsAppChannel: Gagal mengirim.', [
                'phone' => $phone,
                'notifiable_id' => $notifiable->id ?? null,
                'notification' => $notification::class,
            ]);
        }
    }
}
