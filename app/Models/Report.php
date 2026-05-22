<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_user',
        'id_category',
        'jenis_laporan',
        'nama_barang',
        'deskripsi',
        'lokasi',
        'tanggal_kejadian',
        'foto_barang',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
        ];
    }

    // Relasi: Laporan milik seorang user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi: Laporan masuk dalam satu kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    // Relasi: Laporan memiliki banyak klaim
    public function claims()
    {
        return $this->hasMany(Claim::class, 'id_report');
    }

    // Helper: cek apakah laporan bisa diklaim
    public function bisaDiklaim(): bool
    {
        return $this->jenis_laporan === 'temuan' && $this->status === 'approved';
    }

    // Helper: label status dengan badge warna
    public function statusBadge(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved'  => '<span class="badge bg-success">Approved</span>',
            'rejected'  => '<span class="badge bg-danger">Rejected</span>',
            'completed' => '<span class="badge bg-primary">Completed</span>',
            default     => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    // Helper: label jenis laporan
    public function jenisLabel(): string
    {
        return match($this->jenis_laporan) {
            'hilang'  => '<span class="badge bg-danger">Hilang</span>',
            'temuan'  => '<span class="badge bg-success">Temuan</span>',
            default   => '<span class="badge bg-secondary">-</span>',
        };
    }
}