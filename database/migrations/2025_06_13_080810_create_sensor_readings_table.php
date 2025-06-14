<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_bin_type_id')->constrained()->onDelete('cascade');
            $table->decimal('height_cm', 8, 2);
            $table->decimal('percentage', 5, 2);
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
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