<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $this->faker->word,
        ];
    }
}
