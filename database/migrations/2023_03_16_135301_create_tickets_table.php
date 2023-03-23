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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('exhibition_id')->constrained('exhibitions', 'uuid');
            $table->foreignUuid('theater_room_seat_id')->constrained('theater_room_seats', 'uuid');
            $table->foreignUuid('ticket_type_id')->constrained('ticket_types', 'uuid');
            $table->foreignUuid('cart_id')->nullable()->constrained('carts', 'uuid');

            $table->unique(['exhibition_id', 'theater_room_seat_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
