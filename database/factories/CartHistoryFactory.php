<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CartHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'cart_status_id' => CartStatus::factory(),
            'cart_id' => Cart::factory(),
        ];
    }
}
