<?php

namespace Tests\Feature\Repositories;

use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\ExhibitionSeat;
use App\Models\ExhibitionTicketType;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Repositories\MysqlTicketRepository;
use InvalidArgumentException;
use Tests\TestCase;

class MysqlTicketRepositoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider validTicketsInCartDataProvider
     */
    public function should_find_all_the_tickets_in_a_cart_with_their_prices($ticketTypeName, $ticketPrice, $ticketCount)
    {
        $ticketType = TicketType::factory()->create(['name' => $ticketTypeName]);
        $otherTicketType = TicketType::factory()->create(['name' => 'Other']);

        $cartStatus = CartStatus::factory()->create([
            'name' => CartStatus::ACTIVE,
        ]);

        $cart = Cart::factory()->create([
            'cart_status_id' => $cartStatus->uuid
        ]);

        for ($i = 0; $i < $ticketCount; $i++) {
            $ticket = Ticket::factory()->create([
                'cart_id' => $cart->uuid,
                'ticket_type_id' => $ticketType->uuid
            ]);

            ExhibitionSeat::factory()->create([
                'exhibition_id' => $ticket->exhibition_id,
                'theater_room_seat_id' => $ticket->theater_room_seat_id,
            ]);

            ExhibitionSeat::factory()->create([
                'theater_room_seat_id' => $ticket->theater_room_seat_id,
            ]);

            // Create ExhibitionTicketType for the target ticket type
            ExhibitionTicketType::factory()->create([
                'ticket_type_id' => $ticketType->uuid,
                'exhibition_id' => $ticket->exhibition_id,
                'price' => $ticketPrice
            ]);

            // Create ExhibitionTicketType for the other ticket type
            ExhibitionTicketType::factory()->create([
                'ticket_type_id' => $otherTicketType->uuid,
                'exhibition_id' => $ticket->exhibition_id,
                'price' => $ticketPrice + 1000
            ]);
        }

        $sut = resolve(MysqlTicketRepository::class);

        $ticketsInCart = $sut->findTicketsInCart($cart->uuid);

        foreach ($ticketsInCart as $ticketInCart) {
            $this->assertEquals($ticketType->name, $ticketInCart->type->name);
            $this->assertEquals($ticketPrice, $ticketInCart->ticketTypeExhibitionInfo->price);
        }
    }

    /**
     * @test
     * @dataProvider invalidTicketsInCartDataProvider
     */
    public function should_throw_invalid_argument_exception_for_invalid_ticket_prices($ticketTypeName, $ticketPrice, $ticketCount)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->should_find_all_the_tickets_in_a_cart_with_their_prices(
            $ticketTypeName,
            $ticketPrice,
            $ticketCount
        );
    }

    public static function validTicketsInCartDataProvider()
    {
    return [
            [
                TicketType::REGULAR,
                9990,
                5
            ],
            [
                TicketType::STUDENT,
                4995,
                3
            ],
            [
                TicketType::COURTESY,
                0,
                3
            ],
        ];
    }

    public static function invalidTicketsInCartDataProvider()
    {
        return [
            [
                TicketType::REGULAR,
                -1,
                3
            ],
            [
                TicketType::STUDENT,
                -5000,
                3
            ],
        ];
    }
}
