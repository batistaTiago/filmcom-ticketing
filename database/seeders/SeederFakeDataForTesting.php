<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;

class SeederFakeDataForTesting extends Seeder
{
    public function run(): void
    {
//        User::factory()->create(['email' => 'user@test.dev']);
//        Film::factory()->times(15)->create();
//        $theaters = Theater::factory()->times(3)->create();
//        $seatTypes = SeatType::all();
//
//        foreach ($theaters as $theater) {
//            $rooms = TheaterRoom::factory()->times(3)->create(['theater_id' => $theater->uuid]);
//
//            foreach ($rooms as $room) {
//                $rowsCount = rand(6, 10);
//                $seatCount = rand(10, 30);
//
//                for ($i = 0; $i < $rowsCount; $i++) {
//                    $row = TheaterRoomRow::factory()->create([
//                        'name' => Str::orderedUuid()->toString(),
//                        'theater_room_id' => $room->uuid
//                    ]);
//
//                    for ($j = 0; $j < $seatCount; $j++) {
//                        $seatType = $seatTypes[fake()->numberBetween(0, count($seatTypes) - 1)];
//
//                        TheaterRoomSeat::factory()->create([
//                            'name' => Str::orderedUuid()->toString(),
//                            'theater_room_row_id' => $row->uuid,
//                            'seat_type_id' => $seatType
//                        ]);
//                    }
//                }
//            }
//        }

        $cartStatuses = CartStatus::all();
        $ticketTypes = TicketType::all();
        $userIds = User::query()->select('uuid')->pluck('uuid');

        for ($i = 1; $i <= fake()->numberBetween(10, 100); $i++) {
            echo "Creating cart $i" . PHP_EOL;
            $cart = Cart::factory()->create([
                'cart_status_id' => $cartStatuses->random()->uuid,
                'updated_at' => now()->subHours(fake()->numberBetween(-300, -16)),
                'user_id' => $userIds->random()
            ]);

            for ($j = 1; $j <= fake()->numberBetween(1, 5); $j++) {
                echo "Creating ticket $j in cart $i" . PHP_EOL;
                Ticket::factory()->create([
                    'ticket_type_id' => $ticketTypes->random()->uuid,
                    'updated_at' => $cart->updated_at,
                    'cart_id' => $cart->uuid,
                ]);
            }
        }
    }
}
