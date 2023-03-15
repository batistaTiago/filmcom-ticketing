<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TheaterRoomSeatTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
        ];
    }
}
