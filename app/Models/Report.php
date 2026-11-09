<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_category',
        'id_campus',
        'jenis_laporan',
        'nama_barang',
        'deskripsi',
        'lokasi',
        'tanggal_kejadian',
        'foto_barang',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'id_campus');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class, 'id_report');
    }

    /**
     * URL foto: foto dari mobile berupa URL Supabase Storage penuh,
     * foto dari web berupa path relatif di storage lokal.
     */
    public function fotoUrl(): ?string
    {
        if (!$this->foto_barang) {
            return null;
        }

        return str_starts_with($this->foto_barang, 'http')
            ? $this->foto_barang
            : asset('storage/' . $this->foto_barang);
    }

    public function bisaDiklaim(): bool
    {
        return $this->jenis_laporan === 'temuan' && $this->status === 'approved';
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved'  => '<span class="badge bg-success">Disetujui</span>',
            'rejected'  => '<span class="badge bg-danger">Ditolak</span>',
            'completed' => '<span class="badge bg-secondary">Selesai</span>',
            default     => '<span class="badge bg-light text-dark">Unknown</span>',
        };
    }

    public function priorityBadgeSmall(): string
    {
        return $this->category?->priorityBadge() ?? '<span class="findit-badge">-</span>';
    }
}
