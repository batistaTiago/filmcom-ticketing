<?php

namespace Database\Factories;

use App\Models\TheaterRoom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TheaterRoomRowFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $this->faker->word,
            'theater_room_id' => TheaterRoom::factory(),
        ];
    }
}
