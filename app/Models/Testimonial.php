<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_user',
        'id_claim',
        'id_report',
        'isi_testimoni',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /**
     * Relasi: Testimoni milik seorang user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi: Testimoni terkait satu klaim (nullable)
     */
    public function claim()
    {
        return $this->belongsTo(Claim::class, 'id_claim');
    }

    /**
     * Relasi: Testimoni terkait satu laporan
     */
    public function report()
    {
        return $this->belongsTo(Report::class, 'id_report');
    }

    /**
     * Scope: Ambil testimoni terbaru (untuk landing page)
     */
    public function scopeLatestForLanding($query, $limit = 3)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Helper: Generate HTML rating stars
     */
    public function ratingStars(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<span class="star filled">&#9733;</span>'; // ★
            } else {
                $stars .= '<span class="star">&#9734;</span>'; // ☆
            }
        }
        return $stars;
    }

    /**
     * Helper: Get initials from user name
     */
    public function getUserInitials(): string
    {
        $name = $this->user->name ?? 'U';
        $parts = explode(' ', $name);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Helper: Check if user can edit this testimonial
     */
    public function canBeEditedBy($userId): bool
    {
        return $this->id_user === $userId;
    }

    /**
     * Helper: Check if user can delete this testimonial
     */
    public function canBeDeletedBy($userId): bool
    {
        return $this->id_user === $userId;
    }
}