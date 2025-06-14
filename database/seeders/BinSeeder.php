<?php

namespace Database\Seeders;

use App\Models\Bin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wasteBins = [
            [
                'bin_code' => 'WB001',
                'location' => 'Kompleks Perumahan Sawojajar',
                'address' => 'Jl. Soekarno Hatta, Sawojajar, Malang',
                'capacity_liters' => 1.0,
            ],
            [
                'bin_code' => 'WB002',
                'location' => 'Komplek Veteran',
                'address' => 'Jl. Veteran, Lowokwaru, Malang',
                'capacity_liters' => 1.0,
            ],
            [
                'bin_code' => 'WB003',
                'location' => 'Perumahan Diponegoro',
                'address' => 'Jl. Diponegoro, Klojen, Malang',
                'capacity_liters' => 1.0,
            ],
            [
                'bin_code' => 'WB004',
                'location' => 'Komplek Sudirman',
                'address' => 'Jl. Sudirman, Blimbing, Malang',
                'capacity_liters' => 1.0,
            ],
            [
                'bin_code' => 'WB005',
                'location' => 'Perumahan Thamrin',
                'address' => 'Jl. Thamrin, Kedungkandang, Malang',
                'capacity_liters' => 1.0,
            ],
            [
                'bin_code' => 'WB006',
                'location' => 'Kampus Universitas Brawijaya',
                'address' => 'Jl. Mayjen Haryono, Lowokwaru, Malang',
                'capacity_liters' => 2.0,
            ],
            [
                'bin_code' => 'WB007',
                'location' => 'Malang Town Square',
                'address' => 'Jl. Veteran, Klojen, Malang',
                'capacity_liters' => 2.0,
            ],
            [
                'bin_code' => 'WB008',
                'location' => 'Alun-Alun Kota Malang',
                'address' => 'Jl. Merdeka, Klojen, Malang',
                'capacity_liters' => 1.5,
            ],
        ];

        foreach ($wasteBins as $bin) {
            Bin::create($bin);
        }
    }
}
