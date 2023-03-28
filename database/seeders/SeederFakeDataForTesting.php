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
        $defaultUser = User::query()->firstOrCreate(['email' => 'user@test.dev']);
        $cartStatuses = CartStatus::all();
        $ticketTypes = TicketType::all();
        $userIds = User::query()->select('uuid')->pluck('uuid');
        $seatTypes = SeatType::all();

        Film::factory()->times(15)->create();
        $theaters = Theater::factory()->times(3)->create();

        foreach ($theaters as $idx => $theater) {
            // echo "Creating rooms for theater $idx" . PHP_EOL;
            $rooms = TheaterRoom::factory()->times(3)->create(['theater_id' => $theater->uuid]);

            foreach ($rooms as $room) {
                $rowsCount = fake()->numberBetween(6, 10);
                $seatCount = fake()->numberBetween(10, 50);

                for ($jdx = 0; $jdx < $rowsCount; $jdx++) {
                    // echo "Creating row in room $jdx" . PHP_EOL;
                    $row = TheaterRoomRow::factory()->create([
                        'name' => Str::orderedUuid()->toString(),
                        'theater_room_id' => $room->uuid
                    ]);

                    for ($kdx = 0; $kdx < $seatCount; $kdx++) {
                        // echo "Creating seat in row $kdx" . PHP_EOL;
                        $seatType = $seatTypes->random();

                        TheaterRoomSeat::factory()->create([
                            'name' => Str::orderedUuid()->toString(),
                            'theater_room_row_id' => $row->uuid,
                            'seat_type_id' => $seatType->uuid
                        ]);
                    }
                }
            }
        }

        for ($i = 1; $i <= fake()->numberBetween(100, 400); $i++) {
            // echo "Creating cart $i" . PHP_EOL;
            $cart = Cart::factory()->create([
                'cart_status_id' => $cartStatuses->where('name', 'active')->first()->uuid,
                'updated_at' => now()->subHours(fake()->numberBetween(300, 16)),
                'user_id' => $userIds->random()
            ]);

            for ($j = 1; $j <= fake()->numberBetween(1, 5); $j++) {
                // echo "Creating ticket $j in cart $i" . PHP_EOL;
                Ticket::factory()->create([
                    'ticket_type_id' => $ticketTypes->random()->uuid,
                    'updated_at' => $cart->updated_at,
                    'cart_id' => $cart->uuid,
                ]);
            }
        }
    }
}
