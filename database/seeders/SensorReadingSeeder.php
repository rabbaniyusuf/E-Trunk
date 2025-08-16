<?php

namespace Database\Seeders;

use App\Models\SensorReadings;
use Carbon\Carbon;
use App\Models\WasteBinType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SensorReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wasteBinTypes = WasteBinType::all();

        foreach ($wasteBinTypes as $wasteBinType) {
            // Generate readings untuk 7 hari terakhir
            for ($i = 7; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // Generate 3-6 readings per hari
                $readingsPerDay = rand(3, 6);

                for ($j = 0; $j < $readingsPerDay; $j++) {
                    $readingTime = $date->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59));

                    $height = rand(0, 100); // Simulasi tinggi sampah dalam cm
                    $percentage = ($height / 100) * 100;

                    SensorReadings::create([
                        'waste_bin_type_id' => $wasteBinType->id,
                        'percentage' => $percentage,
                        'reading_time' => $readingTime,
                        'created_at' => $readingTime,
                        'updated_at' => $readingTime,
                    ]);
                }
            }
        }
    }
}
