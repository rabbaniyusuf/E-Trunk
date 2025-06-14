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

                    // Simulasi peningkatan sampah secara bertahap
                    $baseHeight = $i == 0 ? $wasteBinType->current_height_cm : rand(5, 60);
                    $height = $baseHeight + rand(-5, 10);
                    $height = max(0, min(100, $height)); // Pastikan dalam range 0-100

                    $percentage = ($height / 100) * 100;

                    SensorReadings::create([
                        'waste_bin_type_id' => $wasteBinType->id,
                        'height_cm' => $height,
                        'percentage' => $percentage,
                        'temperature' => rand(25, 35) + rand(0, 99) / 100, // 25-35°C
                        'humidity' => rand(60, 80) + rand(0, 99) / 100, // 60-80%
                        'reading_time' => $readingTime,
                        'created_at' => $readingTime,
                        'updated_at' => $readingTime,
                    ]);
                }
            }
        }
    }
}
