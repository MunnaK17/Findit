<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_category',
        'priority',
    ];

    protected $casts = [
        'priority' => 'string',
    ];

    // Relasi: Category memiliki banyak laporan
    public function reports()
    {
        return $this->hasMany(Report::class, 'id_category');
    }

    // Helper: Badge priority dengan warna
    public function priorityBadge(): string
    {
        return match($this->priority) {
            'critical' => '<span class="findit-badge" style="background:#fee2e2;color:#dc2626;">🔴 Critical</span>',
            'high' => '<span class="findit-badge" style="background:#fef3c7;color:#d97706;">🟡 High</span>',
            'normal' => '<span class="findit-badge" style="background:#d1fae5;color:#059669;">🟢 Normal</span>',
            default => '<span class="findit-badge">-</span>',
        };
    }

    // List priority options
    public static function priorityOptions(): array
    {
        return [
            'critical' => '🔴 Critical - Barang sangat penting (HP, Dompet, Laptop)',
            'high' => '🟡 High - Barang cukup penting (Tas, документи)',
            'normal' => '🟢 Normal - Barang biasa (Buku, Alat tulis)',
        ];
    }
}