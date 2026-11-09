<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckStudentPhones extends Command
{
    protected $signature = 'check:student-phones';
    protected $description = 'Check which students have/don\'t have phone numbers';

    public function handle()
    {
        $this->info('=== Student Phone Status ===');

        $students = User::where('role', 'mahasiswa')->get();
        $with_phone = $students->filter(fn($u) => $u->phone)->count();
        $without_phone = $students->filter(fn($u) => !$u->phone)->count();

        $this->line("Total students: " . count($students));
        $this->line("With phone: $with_phone ✓");
        $this->line("Without phone: $without_phone ✗");

        if ($without_phone > 0) {
            $this->newLine();
            $this->warn('Students without phone number:');
            $students->filter(fn($u) => !$u->phone)->each(function($u) {
                $this->line("  • ID: {$u->id}, Name: {$u->name}, Email: {$u->email}");
            });
        }

        $this->newLine();
        $this->info('=== Pending Claims Status ===');

        $pending = \App\Models\Claim::with('user')
            ->where('status_klaim', 'pending')
            ->get();

        $this->line("Total pending: " . count($pending));
        if (count($pending) > 0) {
            $pending->each(function($c) {
                $status = $c->user->phone ? '✓' : '✗';
                $this->line("  $status Claim {$c->id}: {$c->user->name} ({$c->user->phone ?? 'NO PHONE'})");
            });
        }
    }
}
