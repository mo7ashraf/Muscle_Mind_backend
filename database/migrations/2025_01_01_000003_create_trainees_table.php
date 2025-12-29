<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
            $table->decimal('current_weight', 6, 2)->nullable();
            $table->decimal('target_weight', 6, 2)->nullable();
            $table->decimal('height', 6, 2)->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('goal', ['weight_loss', 'muscle_gain', 'maintenance'])->default('maintenance');
            $table->date('starting_date')->nullable();
            $table->timestamps();

            $table->index(['trainer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainees');
    }
};
