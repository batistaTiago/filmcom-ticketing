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
        Schema::create('exhibitions', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('film_id')->constrained('films', 'uuid');
            $table->foreignUuid('theater_room_id')->constrained('theater_rooms', 'uuid');
            $table->time('starts_at');
            $table->integer('day_of_week');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibitions');
    }
};
