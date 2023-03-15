<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Film;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\TheaterRoomSeatType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Film::factory()->times(15)->create();
        $theaters = Theater::factory()->times(3)->create();
        $seatTypes = TheaterRoomSeatType::factory()->times(3)->create();

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
                        'theater_room_seat_type_id' => $seatType
                    ]);
                }
            }
        }
    }
}
