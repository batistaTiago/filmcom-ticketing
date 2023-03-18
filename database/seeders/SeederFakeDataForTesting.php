<?php

namespace database\seeders;

use App\Models\Film;
use App\Models\SeatType;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SeederFakeDataForTesting extends Seeder
{
    public function run(): void
    {
        Film::factory()->times(15)->create();
        $theaters = Theater::factory()->times(3)->create();
        $seatTypes = SeatType::factory()->times(3)->create();

        foreach ($theaters as $theater) {
            $rooms = TheaterRoom::factory()->times(3)->create(['theater_id' => $theater->uuid]);

            foreach ($rooms as $room) {
                $rowsCount = rand(2, 3);
                for ($i = 0; $i < $rowsCount; $i++) {
                    $row = TheaterRoomRow::factory()->create([
                        'name' => Str::orderedUuid()->toString(),
                        'theater_room_id' => $room->uuid
                    ]);

                    $seatCount = rand(3, 4);
                    for ($j = 0; $j < $seatCount; $j++) {
                        $seatType = $seatTypes[fake()->numberBetween(0, count($seatTypes) - 1)];

                        TheaterRoomSeat::factory()->create([
                            'name' => Str::orderedUuid()->toString(),
                            'theater_room_row_id' => $row->uuid,
                            'seat_type_id' => $seatType
                        ]);
                    }
                }
            }
        }
    }
}