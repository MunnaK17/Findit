<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $campuses = [
            [
                'nama_kampus' => 'UBSI Kampus Slipi',
                'kode_kampus' => 'SLP',
                'kota'        => 'Jakarta Barat',
                'alamat'      => 'Jl. Kemanggisan Utama No. 8, Slipi, Palmerah, Jakarta Barat',
                'kontak'      => '(021) 53690620',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Kramat 98',
                'kode_kampus' => 'KRM',
                'kota'        => 'Jakarta Pusat',
                'alamat'      => 'Jl. Kramat Raya No. 98, Senen, Jakarta Pusat',
                'kontak'      => '(021) 31922770',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Margonda',
                'kode_kampus' => 'MGD',
                'kota'        => 'Depok',
                'alamat'      => 'Jl. Margonda Raya No. 8, Pondok Cina, Beji, Depok',
                'kontak'      => '(021) 77201416',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus BSD',
                'kode_kampus' => 'BSD',
                'kota'        => 'Tangerang Selatan',
                'alamat'      => 'Jl. Rawa Buntu Raya No. 17, BSD City, Serpong, Tangerang Selatan',
                'kontak'      => '(021) 7566677',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Kaliabang',
                'kode_kampus' => 'KLB',
                'kota'        => 'Bekasi Utara',
                'alamat'      => 'Jl. Kaliabang Raya No. 8, Perwira, Bekasi Utara',
                'kontak'      => '(021) 88984888',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Fatmawati',
                'kode_kampus' => 'FTW',
                'kota'        => 'Jakarta Selatan',
                'alamat'      => 'Jl. RS. Fatmawati No. 24, Pondok Labu, Cilandak, Jakarta Selatan',
                'kontak'      => '(021) 75908888',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Pemuda',
                'kode_kampus' => 'PMD',
                'kota'        => 'Jakarta Timur',
                'alamat'      => 'Jl. Kayu Jati 5 No. 2, Rawamangun, Pulo Gadung, Jakarta Timur',
                'kontak'      => '(021) 47867888',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Cengkareng',
                'kode_kampus' => 'CKG',
                'kota'        => 'Jakarta Barat',
                'alamat'      => 'Jl. Kamal Raya No. 18, Ring Road Barat, Cengkareng, Jakarta Barat',
                'kontak'      => '(021) 54378888',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Ciputat',
                'kode_kampus' => 'CPT',
                'kota'        => 'Tangerang Selatan',
                'alamat'      => 'Jl. Ir. H. Juanda No. 39, Ciputat, Tangerang Selatan',
                'kontak'      => '(021) 7498888',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Cut Mutia',
                'kode_kampus' => 'CTM',
                'kota'        => 'Bekasi Timur',
                'alamat'      => 'Jl. Cut Mutia No. 88, Sepanjang Jaya, Rawalumbu, Bekasi Timur',
                'kontak'      => '(021) 82438888',
                'is_active'   => true,
            ],
            [
                'nama_kampus' => 'UBSI Kampus Karawang',
                'kode_kampus' => 'KRW',
                'kota'        => 'Karawang',
                'alamat'      => 'Jl. Banten No. 1, Karangpawitan, Karawang Barat',
                'kontak'      => '(0267) 8458888',
                'is_active'   => true,
            ],
        ];

        foreach ($campuses as $campus) {
            Campus::updateOrCreate(
                ['kode_kampus' => $campus['kode_kampus']],
                $campus
            );
        }
    }
}
