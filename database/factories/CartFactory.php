<?php

namespace Database\Factories;

use App\Models\CartStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'user_id' => User::factory(),
            'cart_status_id' => CartStatus::factory(),
        ];
    }
}
