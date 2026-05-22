<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'Dompet & Tas',
            'Kartu Identitas',
            'Kunci',
            'Pakaian & Aksesoris',
            'Buku & Alat Tulis',
            'Perhiasan',
            'Lainnya',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['nama_category' => $category]);
        }
    }
}