<?php

namespace App\Notifications;

use App\Mail\ClaimStatusMail;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Notifications\Notification;

class ClaimStatusNotification extends Notification
{
    public function __construct(
        public Claim $claim,
        public Report $report,
        public string $status,
        public ?string $adminNote = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $titles = [
            'pending'  => 'Klaim Submitted',
            'approved' => 'Klaim Disetujui!',
            'rejected' => 'Klaim Ditolak',
        ];

        $bodies = [
            'pending'  => "Klaim kamu untuk {$this->report->nama_barang} sedang menunggu verifikasi admin.",
            'approved' => "Klaim kamu untuk {$this->report->nama_barang} telah disetujui! Silakan datang ke admin kampus di lobby untuk pengambilan barang.",
            'rejected' => "Klaim kamu untuk {$this->report->nama_barang} ditolak.",
        ];

        return [
            'title' => $titles[$this->status] ?? 'Update Klaim',
            'body'  => $bodies[$this->status] ?? 'Ada update klaim.',
            'data'  => [
                'type'       => "claim_{$this->status}",
                'claim_id'   => $this->claim->id,
                'report_id'  => $this->report->id,
                'url'        => route('my.claims'),
            ],
        ];
    }

    public function toMail(object $notifiable)
    {
        return new ClaimStatusMail($this->claim, $this->report, $this->status, $this->adminNote);
    }
}

