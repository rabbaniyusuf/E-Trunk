<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_bin_type_id')->constrained()->onDelete('cascade');
            $table->decimal('percentage', 5, 2); // Hanya persentase yang dibutuhkan
            $table->timestamp('reading_time')->useCurrent();
            $table->timestamps();

            $table->index(['reading_time']);
            $table->index(['waste_bin_type_id', 'reading_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
