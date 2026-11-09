<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Notifikasi in-app bersama web & mobile (tabel Supabase public.app_notifications).
 * Baris dibuat Edge Function notify-email setiap status laporan/klaim berubah.
 */
class AppNotification extends Model
{
    protected $table = 'app_notifications';

    public $timestamps = false;

    protected $fillable = ['read_at'];

    protected function casts(): array
    {
        return [
            'read_at'    => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Halaman tujuan saat notifikasi diklik. */
    public function url(): string
    {
        return $this->report_id ? route('reports.show', $this->report_id) : route('notifications.index');
    }
}
