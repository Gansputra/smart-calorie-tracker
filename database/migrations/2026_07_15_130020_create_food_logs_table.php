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
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('food_id')->nullable();
            $table->foreign('food_id')->references('id')->on('foods')->nullOnDelete();
            $table->string('food_name');
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbs', 8, 2)->nullable();
            $table->decimal('fat', 8, 2)->nullable();
            $table->decimal('portion', 5, 2)->default(1.00); // e.g. 1.5 = 1.5 serving (100g each)
            $table->enum('meal_type', ['Sarapan', 'Makan Siang', 'Makan Malam', 'Camilan'])->default('Sarapan');
            $table->text('notes')->nullable();
            $table->date('date');
            $table->boolean('ai_detected')->default(false);
            $table->decimal('ai_confidence', 5, 4)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_logs');
    }
};
