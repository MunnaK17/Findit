<?php

namespace App\Events;

use App\Models\Claim;
use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClaimStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Claim $claim,
        public Report $report,
        public string $status
    ) {}

    public function broadcastOn(): array
    {
        // Broadcast ke channel private user yang klaim
        return [new PrivateChannel('user.' . $this->claim->id_user)];
    }

    public function broadcastAs(): string
    {
        return 'claim.status';
    }

    public function broadcastWith(): array
    {
        $titles = [
            'pending'  => 'Klaim Submitted',
            'approved' => 'Klaim Disetujui!',
            'rejected' => 'Klaim Ditolak',
        ];

        return [
            'claim_id' => $this->claim->id,
            'report_id' => $this->report->id,
            'barang'   => $this->report->nama_barang,
            'status'   => $this->status,
            'title'    => $titles[$this->status] ?? 'Update Klaim',
            'message'  => $this->status === 'approved'
                ? "Klaim {$this->report->nama_barang} disetujui!"
                : ($this->status === 'rejected'
                    ? "Klaim {$this->report->nama_barang} ditolak."
                    : "Klaim {$this->report->nama_barang} menunggu verifikasi."),
        ];
    }
}