<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kampus',
        'kode_kampus',
        'kota',
        'alamat',
        'kontak',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_campus');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'id_campus');
    }
}
