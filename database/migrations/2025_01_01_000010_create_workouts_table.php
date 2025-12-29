<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable()->index();
            $table->boolean('completed')->default(false)->index();
            $table->timestamps();

            $table->index(['trainee_id', 'trainer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
