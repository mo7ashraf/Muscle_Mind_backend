<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->string('name');
            $table->string('time')->nullable(); // e.g., "08:00"
            $table->unsignedInteger('calories')->nullable();
            $table->decimal('proteins', 6, 2)->nullable();
            $table->decimal('carbs', 6, 2)->nullable();
            $table->decimal('fats', 6, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['diet_plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
