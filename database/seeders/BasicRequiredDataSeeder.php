<?php

namespace database\seeders;

use App\Models\Film;
use App\Models\SeatStatus;
use App\Models\SeatType;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\TicketType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BasicRequiredDataSeeder extends Seeder
{
    public function run(): void
    {
        TicketType::factory()->create(['name' => TicketType::REGULAR]);
        TicketType::factory()->create(['name' => TicketType::STUDENT]);
        TicketType::factory()->create(['name' => TicketType::COURTESY]);

        SeatStatus::factory()->create(['name' => SeatStatus::AVAILABLE]);
        SeatStatus::factory()->create(['name' => SeatStatus::RESERVED]);
        SeatStatus::factory()->create(['name' => SeatStatus::SOLD]);
        SeatStatus::factory()->create(['name' => SeatStatus::UNAVAILABLE]);

        SeatType::factory()->create(['name' => SeatType::REGULAR]);
        SeatType::factory()->create(['name' => SeatType::LARGE]);
        SeatType::factory()->create(['name' => SeatType::WHEEL_CHAIR]);
    }
}
