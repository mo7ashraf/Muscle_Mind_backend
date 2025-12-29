<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->string('front_image')->nullable();
            $table->string('back_image')->nullable();
            $table->string('side_image')->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('taken_at')->useCurrent();
            $table->timestamps();

            $table->index(['trainee_id', 'taken_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_photos');
    }
};
