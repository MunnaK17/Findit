<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Pengguna FindIT. Data disimpan di tabel `profiles` (Supabase PostgreSQL);
 * kredensial & verifikasi email ditangani oleh Supabase Auth.
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'profiles';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'nim',
        'nip',
        'role',
        'phone',
        'id_campus',
        'prodi_unit',
        'avatar_url',
    ];

    /** Kredensial dikelola Supabase, bukan Laravel. */
    public function getAuthPassword()
    {
        return '';
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName()
    {
        return '';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /** NIM untuk mahasiswa, NIP untuk staff. */
    public function nomorIndukLabel(): string
    {
        return $this->isStaff() ? 'NIP' : 'NIM';
    }

    public function nomorInduk(): ?string
    {
        return $this->isStaff() ? $this->nip : $this->nim;
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'id_campus');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'id_user');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class, 'id_user');
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'id_user');
    }
}
