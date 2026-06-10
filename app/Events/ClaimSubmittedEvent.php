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

class ClaimSubmittedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Claim $claim,
        public Report $report
    ) {}

    public function broadcastOn(): array
    {
        // Broadcast ke channel admin
        return [new Channel('admin')];
    }

    public function broadcastAs(): string
    {
        return 'claim.submitted';
    }

    public function broadcastWith(): array
    {
        return [
            'claim_id'  => $this->claim->id,
            'report_id' => $this->report->id,
            'barang'    => $this->report->nama_barang,
            'user_name' => $this->claim->user->name,
            'lokasi'   => $this->report->lokasi,
            'message'   => "Klaim baru: {$this->claim->user->name} → {$this->report->nama_barang}",
        ];
    }
}