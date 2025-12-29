<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->decimal('weight', 6, 2)->nullable();
            $table->decimal('chest', 6, 2)->nullable();
            $table->decimal('waist', 6, 2)->nullable();
            $table->decimal('hips', 6, 2)->nullable();
            $table->decimal('arms', 6, 2)->nullable();
            $table->decimal('thighs', 6, 2)->nullable();
            $table->timestamp('measured_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['trainee_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
