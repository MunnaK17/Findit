<?php require 'bootstrap/app.php';

$students = \App\Models\User::where('role', 'mahasiswa')->get();
$with_phone = 0;
$without_phone = 0;

foreach ($students as $s) {
    if ($s->phone) $with_phone++;
    else $without_phone++;
}

echo "Total students: " . count($students) . "\n";
echo "With phone: " . $with_phone . "\n";
echo "Without phone: " . $without_phone . "\n\n";

echo "Students without phone:\n";
foreach ($students as $u) {
    if (!$u->phone) {
        echo "  - ID:".$u->id.", ".$u->name.", ".$u->email."\n";
    }
}

echo "\nPending claims:\n";
$pending = \App\Models\Claim::with('user')->where('status_klaim', 'pending')->get();
foreach ($pending as $c) {
    $phone = $c->user->phone ? $c->user->phone : 'NO PHONE';
    echo "  - Claim ".$c->id.": ".$c->user->name." (".$phone.")\n";
}
