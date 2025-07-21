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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // collection_request, point_transaction, schedule_update, system
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data
            $table->timestamp('read_at')->nullable();
            $table->string('notifiable_type')->nullable(); // Polymorphic relation
            $table->unsignedBigInteger('notifiable_id')->nullable(); // Polymorphic relation
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'read_at']);
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};