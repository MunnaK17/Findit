<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Daftar kategori
    public function index()
    {
        $categories = Category::withCount('reports')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_category' => ['required', 'string', 'max:100', 'unique:categories,nama_category'],
        ]);

        Category::create(['nama_category' => $request->nama_category]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Form edit kategori
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    // Update kategori
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama_category' => ['required', 'string', 'max:100', 'unique:categories,nama_category,' . $id],
        ]);

        $category->update(['nama_category' => $request->nama_category]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // Hapus kategori
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Cek apakah kategori masih dipakai
        if ($category->reports()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kategori tidak bisa dihapus karena masih digunakan.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}