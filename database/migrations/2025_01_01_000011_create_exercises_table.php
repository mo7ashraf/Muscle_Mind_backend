<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained('workouts')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('sets')->nullable();
            $table->unsignedInteger('reps')->nullable();
            $table->unsignedInteger('rest_time')->nullable(); // seconds
            $table->text('notes')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();

            $table->index(['workout_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
