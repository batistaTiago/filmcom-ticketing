<?php

namespace Database\Factories;

use App\Models\TheaterRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class TheaterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
        ];
    }
}
