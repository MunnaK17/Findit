<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek dulu, hindari duplikat
        if (!User::where('email', 'admin@findit.com')->exists()) {
            User::create([
                'name'     => 'Admin FindIt',
                'email'    => 'admin@findit.com',
                'password' => Hash::make('admin123'),
                'nim'      => '19241331',
                'role'     => 'admin',
            ]);
        }
    }
}