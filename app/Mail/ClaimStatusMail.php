<?php

namespace App\Mail;

use App\Models\Claim;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Claim $claim,
        public Report $report,
        public string $status,
 public ?string $adminNote = null
    ) {}

    public function envelope(): Envelope
    {
        $titles = [
            'pending'   => 'Klaim Baru Submitted',
            'approved'  => 'Klaim Disetujui!',
            'rejected'  => 'Klaim Ditolak',
        ];

        return new Envelope(
            to: $this->claim->user->email,
            subject: $titles[$this->status] ?? 'Update Klaim FindIT'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.claim-status',
            with: [
                'claim'      => $this->claim,
                'report'     => $this->report,
                'status'     => $this->status,
                'adminNote'  => $this->adminNote,
            ]
        );
    }
}
