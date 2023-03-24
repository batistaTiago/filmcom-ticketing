<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Exhibition;
use App\Models\TheaterRoomSeat;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'exhibition_id' => Exhibition::factory(),
            'theater_room_seat_id' => TheaterRoomSeat::factory(),
            'ticket_type_id' => TicketType::factory(),
            'cart_id' => Cart::factory()->create()
        ];
    }
}
