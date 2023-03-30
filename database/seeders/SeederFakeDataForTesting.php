<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\Film;
use App\Models\SeatType;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SeederFakeDataForTesting extends Seeder
{
    public function run(): void
    {
        $this->createDefaultUser();
        $seatTypes = SeatType::all();

        Film::factory()->times(15)->create();
        $theaters = Theater::factory()->times(3)->create();

        foreach ($theaters as $theater) {
            // echo "Creating rooms for theater $i" . PHP_EOL;
            $rooms = TheaterRoom::factory()->times(3)->create(['theater_id' => $theater->uuid]);

            foreach ($rooms as $room) {
                $rowsCount = fake()->numberBetween(6, 10);
                $seatCount = fake()->numberBetween(10, 50);

                for ($i = 0; $i < $rowsCount; $i++) {
                    // echo "Creating row in room $i" . PHP_EOL;
                    $row = TheaterRoomRow::factory()->create([
                        'name' => strtoupper(chr(64 + ($i+1))),
                        'theater_room_id' => $room->uuid
                    ]);

                    for ($j = 0; $j < $seatCount; $j++) {
                        // echo "Creating seat in row $j" . PHP_EOL;
                        $seatType = $seatTypes->random();

                        TheaterRoomSeat::factory()->create([
                            'name' => $j+1,
                            'theater_room_row_id' => $row->uuid,
                            'seat_type_id' => $seatType->uuid
                        ]);
                    }
                }
            }
        }
    }

    private function createDefaultUser()
    {
        $uuid = Str::orderedUuid()->toString();
        $email = 'admin@filmcom.com';

        return User::query()->firstOrCreate(
            compact('email'),
            User::factory()->raw(
                compact('uuid', 'email')
            )
        );
    }
}
