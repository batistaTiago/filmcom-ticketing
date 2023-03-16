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
        Schema::create('exhibition_seats', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('seat_status_id')->constrained('seat_statuses', 'uuid');
            $table->foreignUuid('exhibition_id')->constrained('exhibitions', 'uuid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibition_seats');
    }
};
