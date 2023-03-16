<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SeatStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->fake->uuid,
            'name' => $this->faker->word,
        ];
    }
}
