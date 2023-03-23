<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\SeatType;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SeederFakeDataForTesting extends Seeder
{
    public function run(): void
    {
        User::factory()->create(['email' => 'user@test.dev']);
        Film::factory()->times(15)->create();
        $theaters = Theater::factory()->times(3)->create();
        $seatTypes = SeatType::all();

        foreach ($theaters as $theater) {
            $rooms = TheaterRoom::factory()->times(3)->create(['theater_id' => $theater->uuid]);

            foreach ($rooms as $room) {
                $rowsCount = rand(6, 10);
                $seatCount = rand(10, 30);

                for ($i = 0; $i < $rowsCount; $i++) {
                    $row = TheaterRoomRow::factory()->create([
                        'name' => Str::orderedUuid()->toString(),
                        'theater_room_id' => $room->uuid
                    ]);

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
