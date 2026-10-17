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
        Schema::create('salon_opening_hours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('salon_id')
                ->constrained('salons')
                ->cascadeOnDelete();

            // 1 = Monday, 7 = Sunday
            $table->unsignedTinyInteger('day_of_week');

            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();

            $table->boolean('is_closed')->default(false);

            $table->timestamps();

            $table->unique(['salon_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salon_opening_hours');
    }
};
