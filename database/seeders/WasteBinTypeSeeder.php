<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\WasteBinType;
use Illuminate\Database\Seeder;

class WasteBinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wasteBins = Bin::all();

        foreach ($wasteBins as $wasteBin) {
            // Recycle bin
            WasteBinType::create([
                'bin_id' => $wasteBin->id,
                'type' => 'recycle',
                'current_percentage' => rand(0, 30),
                'last_sensor_reading' => now()->subMinutes(rand(1, 60)),
            ]);

            // Non-recycle bin
            WasteBinType::create([
                'bin_id' => $wasteBin->id,
                'type' => 'non_recycle',
                'current_percentage' => rand(0, 40),
                'last_sensor_reading' => now()->subMinutes(rand(1, 60)),
            ]);
        }

        // Update beberapa bin menjadi hampir penuh untuk testing
        $fullBins = WasteBinType::inRandomOrder()->limit(4)->get();
        foreach ($fullBins as $bin) {
            $percentage = rand(75, 95);
            $bin->update([
                'current_percentage' => $percentage,
                'last_sensor_reading' => now()->subMinutes(rand(5, 30)),
            ]);
        }
    }
}
