<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin FindIT',
            'email' => 'admin@findit.com',
            'password' => Hash::make('password'),
            'nim' => '0000000000',
            'role' => 'admin',
        ]);

        // Create regular users
        $user1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@findit.com',
            'password' => Hash::make('password'),
            'nim' => '2024001001',
            'role' => 'mahasiswa',
        ]);

        $user2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@findit.com',
            'password' => Hash::make('password'),
            'nim' => '2024001002',
            'role' => 'mahasiswa',
        ]);

        $user3 = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'ahmad@findit.com',
            'password' => Hash::make('password'),
            'nim' => '2024001003',
            'role' => 'mahasiswa',
        ]);

        // Create categories
        $categories = [
            'Elektronik',
            'Tas & Dompet',
            'Kunci & Kartu',
            'Pakaian',
            'Buku & Alat Tulis',
            'Perhiasan',
            'Lainnya',
        ];

        foreach ($categories as $cat) {
            Category::create(['nama_category' => $cat]);
        }

        // Create sample reports
        Report::create([
            'id_user' => $user1->id,
            'id_category' => 1,
            'jenis_laporan' => 'hilang',
            'nama_barang' => 'Laptop ASUS ROG',
            'deskripsi' => 'Laptop warna hitam dengan stiker gaming, hilang di perpustakaan kampus sekitar jam 2 siang',
            'lokasi' => 'Perpustakaan Pusat',
            'tanggal_kejadian' => now()->subDays(5)->toDateString(),
            'status' => 'approved',
        ]);

        Report::create([
            'id_user' => $user2->id,
            'id_category' => 2,
            'jenis_laporan' => 'temuan',
            'nama_barang' => 'Dompet Kulit Coklat',
            'deskripsi' => 'Dompet kulit coklat tua, berisi kartu identitas atas nama Rudi Hermawan, ditemukan di kantin utama',
            'lokasi' => 'Kantin Utama',
            'tanggal_kejadian' => now()->subDays(3)->toDateString(),
            'status' => 'approved',
        ]);

        Report::create([
            'id_user' => $user3->id,
            'id_category' => 3,
            'jenis_laporan' => 'temuan',
            'nama_barang' => 'Kunci Rumah & Kartu Mahasiswa',
            'deskripsi' => 'Satu set kunci rumah dengan gantungan biru dan kartu mahasiswa, ditemukan di depan gedung A',
            'lokasi' => 'Gedung A',
            'tanggal_kejadian' => now()->subDays(2)->toDateString(),
            'status' => 'approved',
        ]);

        Report::create([
            'id_user' => $user1->id,
            'id_category' => 4,
            'jenis_laporan' => 'hilang',
            'nama_barang' => 'Jaket Hoodie Biru Navy',
            'deskripsi' => 'Jaket hoodie biru navy dengan logo universitas, hilang di ruang olahraga',
            'lokasi' => 'Ruang Olahraga',
            'tanggal_kejadian' => now()->subDays(1)->toDateString(),
            'status' => 'pending',
        ]);
    }
}
