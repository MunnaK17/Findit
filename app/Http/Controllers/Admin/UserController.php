<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SupabaseAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Daftar semua user
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $search = '%' . addcslashes($request->search, '%_\\') . '%';

            $query->where(function ($query) use ($search) {
                $query->where('name', 'ilike', $search)
                    ->orWhere('email', 'ilike', $search)
                    ->orWhere('nim', 'ilike', $search)
                    ->orWhere('nip', 'ilike', $search)
                    ->orWhere('prodi_unit', 'ilike', $search)
                    ->orWhere('phone', 'ilike', $search);
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // Hapus user: akun Supabase Auth dihapus, profil/klaim ikut lewat foreign key.
    public function destroy($id, SupabaseAuth $supabase)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Hanya menghapus baris profiles membuat pengguna tetap bisa login dan profilnya dibuat ulang.
        $result = $supabase->deleteUser($user->id);
        if (!$result['ok']) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus akun: ' . $result['message']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
