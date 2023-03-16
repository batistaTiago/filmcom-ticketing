<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Film;
use App\Models\SeatStatus;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\SeatType;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TicketType::factory()->create(['name' => TicketType::REGULAR]);
        TicketType::factory()->create(['name' => TicketType::STUDENT]);
        TicketType::factory()->create(['name' => TicketType::COURTESY]);

        SeatStatus::factory()->create(['name' => SeatStatus::REGULAR]);
        SeatStatus::factory()->create(['name' => SeatStatus::LARGE]);
        SeatStatus::factory()->create(['name' => SeatStatus::WHEEL_CHAIR]);

        Film::factory()->times(15)->create();
        $theaters = Theater::factory()->times(3)->create();
        $seatTypes = SeatType::factory()->times(3)->create();

        foreach ($theaters as $theater) {
            $rooms = TheaterRoom::factory()->times(3)->create(['theater_id' => $theater->uuid]);

            foreach ($rooms as $room) {
                $rows = TheaterRoomRow::factory()->times(rand(2, 3))->create([
                    'theater_room_id' => $room->uuid
                ]);

                foreach ($rows as $row) {
                    $seatType = $seatTypes[fake()->numberBetween(0, count($seatTypes) - 1)];

                    TheaterRoomSeat::factory()->times(rand(3, 4))->create([
                        'theater_room_row_id' => $row->uuid,
                        'seat_type_id' => $seatType
                    ]);
                }
            }
        }
    }
}
