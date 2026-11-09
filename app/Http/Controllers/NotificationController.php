<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;

/**
 * Lonceng notifikasi pengguna. Sumber datanya tabel app_notifications yang sama dengan
 * aplikasi mobile, jadi notifikasi yang dibaca di satu sisi ikut terbaca di sisi lain.
 */
class NotificationController extends Controller
{
    private function mine()
    {
        return AppNotification::where('user_id', Auth::id());
    }

    public function index()
    {
        $notifications = $this->mine()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function apiIndex()
    {
        $notifications = $this->mine()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn (AppNotification $n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'body'       => $n->body,
                'data'       => ['url' => $n->url(), 'event' => $n->event],
                'read_at'    => $n->read_at?->toISOString(),
                'created_at' => $n->created_at?->toISOString(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $this->mine()->whereNull('read_at')->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $this->mine()->whereKey($id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        $this->mine()->whereNull('read_at')->update(['read_at' => now()]);

        // Dipanggil lewat fetch (lonceng) maupun form biasa (halaman Notifikasi).
        return request()->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
