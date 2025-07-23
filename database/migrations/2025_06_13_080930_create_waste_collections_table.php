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
            $table->decimal('estimated_weight_kg', 8, 2)->nullable()
                ->comment('Perkiraan berat sampah dalam kilogram (diisi oleh user)');
            $table->decimal('actual_weight_kg', 8, 2)->nullable()
                ->comment('Berat sampah aktual dalam kilogram (diisi oleh petugas)');
            $table->integer('points_earned')->nullable()
                ->comment('Total poin yang diperoleh dari sampah');
            $table->enum('points_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Status approval poin oleh admin');
            $table->foreignId('points_approved_by')->nullable()
                ->constrained('users')->nullOnDelete()
                ->comment('Admin yang meng-approve poin');
            $table->timestamp('points_approved_at')->nullable()
                ->comment('Waktu poin di-approve');
            $table->text('points_rejection_reason')->nullable()
                ->comment('Alasan penolakan poin');

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
            $table->index(['points_status', 'created_at'], 'idx_points_status_created');
            $table->index(['points_approved_by', 'points_approved_at'], 'idx_points_approval');
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