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
        Schema::create('waste_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('waste_bin_type_id')->constrained()->onDelete('cascade');
            $table->json('waste_types'); // ['kardus', 'plastik', 'kertas']
            $table->date('pickup_date');
            $table->string('pickup_time'); // '08:00-10:00', '10:00-12:00', etc.
            $table->enum('status', [
                'MENUNGGU_JADWAL',
                'TERJADWAL',
                'SEDANG_DIPROSES',
                'SELESAI',
                'DIBATALKAN'
            ])->default('MENUNGGU_JADWAL');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['pickup_date', 'pickup_time']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_collections');
    }
};
