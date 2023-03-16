<?php

namespace Database\Factories;

use App\Models\Exhibition;
use App\Models\TheaterRoomSeat;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->fake->uuid,
            'exhibition_id' => Exhibition::factory(),
            'theater_room_seat_id' => TheaterRoomSeat::factory(),
            'ticket_type_id' => TicketType::factory(),
        ];
    }
}
