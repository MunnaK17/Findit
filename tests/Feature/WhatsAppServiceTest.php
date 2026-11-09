<?php

namespace Tests\Feature;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    public function test_it_sends_message_to_twilio_with_normalized_target(): void
    {
        config([
            'services.twilio.account_sid' => 'test-sid',
            'services.twilio.auth_token' => 'test-token',
            'services.twilio.whatsapp_from' => 'whatsapp:+14155238886',
        ]);

        Http::fake([
            '*api.twilio.com*' => Http::response([
                'sid' => 'SM1234567890',
                'status' => 'queued',
                'to' => 'whatsapp:+6281234567890',
                'from' => 'whatsapp:+14155238886',
                'body' => 'Test message tanpa link',
            ]),
        ]);

        $sent = app(WhatsAppService::class)->send('0812-3456-7890', 'Test message tanpa link');

        $this->assertTrue($sent);
    }
}

