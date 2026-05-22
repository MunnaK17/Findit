<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_report',
        'id_user',
        'pesan_klaim',
        'status_klaim',
        'tanggal_klaim',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_klaim' => 'date',
        ];
    }

    // Relasi: Klaim milik satu laporan
    public function report()
    {
        return $this->belongsTo(Report::class, 'id_report');
    }

    // Relasi: Klaim milik seorang user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Helper: label status klaim dengan badge warna
    public function statusBadge(): string
    {
        return match($this->status_klaim) {
            'pending'  => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default    => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}