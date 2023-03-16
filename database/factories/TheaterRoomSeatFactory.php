<?php

namespace Database\Factories;

use App\Models\TheaterRoomRow;
use App\Models\SeatType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TheaterRoomSeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->word,
            'theater_room_row_id' => TheaterRoomRow::factory(),
            'seat_type_id' => SeatType::factory(),
        ];
    }
}
