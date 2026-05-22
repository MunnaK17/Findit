<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Daftar semua user
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('nim', 'like', '%' . $request->search . '%');
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Jangan sampai admin hapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}