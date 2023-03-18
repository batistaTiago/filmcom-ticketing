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
        Schema::create('theater_room_seats', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->foreignUuid('seat_type_id')->constrained('seat_types', 'uuid');
            $table->foreignUuid('theater_room_row_id')->constrained('theater_room_rows', 'uuid');

            $table->unique(['name', 'theater_room_row_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theater_room_seats');
    }
};
