<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;
use App\Models\Claim;
use App\Models\Report;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if students have phone numbers
$students = User::where('role', 'mahasiswa')->get();
echo "=== Checking Students ===\n";
echo "Total students: " . count($students) . "\n";
echo "Students with phone: " . $students->filter(fn($u) => $u->phone)->count() . "\n";
echo "Students without phone: " . $students->filter(fn($u) => !$u->phone)->count() . "\n";

$students->filter(fn($u) => !$u->phone)->each(function($u) {
    echo "\n[No Phone] ID: {$u->id}, Name: {$u->name}, Email: {$u->email}\n";
});

// Check pending claims
echo "\n=== Checking Claims ===\n";
$claims = Claim::where('status_klaim', 'pending')->get();
echo "Pending claims: " . count($claims) . "\n";

$claims->each(function($c) {
    $user = $c->user;
    $phone_status = $user->phone ? "✓ {$user->phone}" : "✗ NO PHONE";
    echo "- Claim ID: {$c->id}, User: {$user->name} [{$phone_status}]\n";
});
