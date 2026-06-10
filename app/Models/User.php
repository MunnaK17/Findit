<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Cek apakah user adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Relasi: User memiliki banyak laporan
    public function reports()
    {
        return $this->hasMany(Report::class, 'id_user');
    }

    // Relasi: User memiliki banyak klaim
    public function claims()
    {
        return $this->hasMany(Claim::class, 'id_user');
    }

    // Relasi: User memiliki banyak testimoni
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'id_user');
    }
}