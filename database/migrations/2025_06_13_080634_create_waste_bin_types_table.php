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
         Schema::create('waste_bin_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bin_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['recycle', 'non_recycle']);
            $table->decimal('current_height_cm', 8, 2)->default(0.00);
            $table->decimal('max_height_cm', 8, 2)->default(100.00);
            $table->decimal('current_percentage', 5, 2)->default(0.00);
            $table->timestamp('last_sensor_reading')->nullable();
            $table->timestamps();

            $table->index(columns: ['bin_id', 'type']);
            $table->index(['current_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_bin_types');
    }
};
