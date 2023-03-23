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
        Schema::create('exhibition_ticket_types', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('exhibition_id')->constrained('exhibitions', 'uuid');
            $table->foreignUuid('ticket_type_id')->constrained('ticket_types', 'uuid');

            $table->bigInteger('price');

            $table->unique(['exhibition_id', 'ticket_type_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibition_ticket_types');
    }
};
