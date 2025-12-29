<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'failed'])->default('ongoing')->index();
            $table->unsignedInteger('completed_days')->default(0);
            $table->date('last_check_in')->nullable();
            $table->timestamps();

            $table->unique(['trainee_id', 'challenge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_challenges');
    }
};
