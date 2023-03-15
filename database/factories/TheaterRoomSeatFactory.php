<?php

namespace Database\Factories;

use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeatType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TheaterRoomSeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
            'theater_room_row_id' => TheaterRoomRow::factory(),
            'theater_room_seat_type_id' => TheaterRoomSeatType::factory(),
        ];
    }
}
