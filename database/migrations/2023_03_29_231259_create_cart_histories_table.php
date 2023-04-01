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
        Schema::create('cart_history', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('cart_id')->constrained('carts', 'uuid');
            $table->foreignUuid('cart_status_id')->constrained('cart_statuses', 'uuid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_history');
    }
};
