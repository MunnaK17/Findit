<?php

namespace App\Notifications;

use App\Mail\ReportStatusMail;
use App\Models\Report;
use Illuminate\Notifications\Notification;

class ReportSubmittedNotification extends Notification
{
    public function __construct(
        public Report $report,
        public string $jenis
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $titles = [
            'hilang' => 'Laporan Barang Hilang Submitted',
            'temuan' => 'Laporan Barang Temuan Submitted',
        ];

        $bodies = [
            'hilang' => "Laporan kehilangan {$this->report->nama_barang} telah kami terima dan sedang menunggu verifikasi admin.",
            'temuan' => "Laporan temuan {$this->report->nama_barang} telah kami terima. Silakan antar barang ke resepsionis kampus.",
        ];

        return [
            'title' => $titles[$this->jenis] ?? 'Update Laporan',
            'body'  => $bodies[$this->jenis] ?? 'Ada update laporan.',
            'data'  => [
                'type'       => "report_{$this->jenis}",
                'report_id'  => $this->report->id,
                'url'        => route('my.reports'),
            ],
        ];
    }

    public function toMail(object $notifiable)
    {
        return new ReportStatusMail($this->report, $this->jenis);
    }
}
