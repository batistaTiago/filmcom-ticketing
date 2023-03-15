<?php

namespace Database\Factories;

use App\Models\TheaterRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class TheaterRoomRowFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
            'theater_room_id' => TheaterRoom::factory(),
        ];
    }
}
