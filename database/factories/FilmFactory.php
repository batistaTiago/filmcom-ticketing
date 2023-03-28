<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FilmFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'name' => fake()->catchPhrase,
            'year' => $this->faker->year,
            'duration' => $this->faker->numberBetween(90, 180)
        ];
    }
}
