<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Report $report,
        public string $jenis,
        public ?string $adminNote = null
    ) {}

    public function envelope(): Envelope
    {
        $titles = [
            'hilang' => 'Laporan Barang Hilang Submitted',
            'temuan' => 'Laporan Barang Temuan Submitted',
        ];

        return new Envelope(
            to: $this->report->user->email,
            subject: $titles[$this->jenis] ?? 'Update Laporan FindIT'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.report-submitted',
            with: [
                'report'     => $this->report,
                'jenis'     => $this->jenis,
                'adminNote' => $this->adminNote,
            ]
        );
    }
}
