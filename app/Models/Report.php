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

    /** Masa tayang laporan yang baru disetujui, sama dengan default kolom expires_at. */
    public const MASA_TAYANG_HARI = 60;

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'expires_at'       => 'datetime',
        ];
    }

    /** Laporan yang boleh tampil di daftar publik: terverifikasi/selesai dan belum kedaluwarsa. */
    public function scopePubliclyVisible($query)
    {
        return $query->whereIn('status', ['approved', 'completed'])
            ->where('expires_at', '>', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /** Laporan belum diverifikasi/kedaluwarsa hanya boleh dilihat pemilik dan admin. */
    public function isVisibleTo(?User $user): bool
    {
        if ($user && ($user->isAdmin() || $user->id === $this->id_user)) {
            return true;
        }

        return in_array($this->status, ['approved', 'completed'], true) && !$this->isExpired();
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
     * URL foto: URL Supabase Storage penuh (mobile & web sekarang), atau path relatif
     * di storage lokal untuk laporan web lama.
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
        return $this->jenis_laporan === 'temuan' && $this->status === 'approved' && !$this->isExpired();
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
