<?php

namespace App\Notifications;

use App\Mail\ClaimStatusMail;
use App\Models\Claim;
use App\Models\Report;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClaimSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Claim $claim,
        public Report $report
    ) {}

    public function via(object $notifiable): array
    {
        return [DatabaseChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Klaim Baru Submitted',
            'body'  => "{$this->claim->user->name} mengajukan klaim untuk {$this->report->nama_barang}",
            'data'  => [
                'type'       => 'claim_submitted',
                'claim_id'   => $this->claim->id,
                'report_id'  => $this->report->id,
                'url'        => route('admin.claims.show', $this->claim->id),
            ],
        ];
    }
}
