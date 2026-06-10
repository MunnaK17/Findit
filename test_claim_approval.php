<?php require 'bootstrap/app.php';

$claim = \App\Models\Claim::where('status_klaim', 'pending')->first();
if (!$claim) {
    echo "Tidak ada pending claim\n";
    exit(1);
}

echo "Testing claim approval...\n";
echo "Claim ID: {$claim->id}\n";
echo "Student: {$claim->user->name} ({$claim->user->email})\n";
echo "Item: {$claim->report->nama_barang}\n\n";

// Approve
$claim->update(['status_klaim' => 'approved']);
$claim->report->update(['status' => 'completed']);

// Send notification
$claim->user->notify(new \App\Notifications\ClaimStatusNotification($claim, $claim->report, 'approved'));

echo "✓ Claim approved + notification sent\n";
