<?php

namespace Database\Factories;

use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TheaterRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $this->faker->word,
            'theater_id' => Theater::factory(),
        ];
    }
}
