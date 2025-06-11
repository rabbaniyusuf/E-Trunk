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
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bin_code')->unique();
            $table->enum('type', ['recycle', 'non_recycle']);
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->decimal('current_weight', 8, 2)->default(0);
            $table->decimal('capacity', 8, 2)->default(100); // kg
            $table->timestamp('last_pickup')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bins');
    }
};
