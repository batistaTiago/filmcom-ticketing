<?php

namespace Database\Factories;

use App\Models\TheaterRoom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TheaterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $this->faker->word,
        ];
    }
}
