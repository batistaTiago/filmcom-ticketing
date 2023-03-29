<?php

namespace Tests\Feature\Repositories;

use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\Exhibition;
use App\Models\ExhibitionSeat;
use App\Models\ExhibitionTicketType;
use App\Models\SeatStatus;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Tests\TestCase;

class MysqlCartRepositoryTest extends TestCase
{
    /**
     * @dataProvider updateStatusDataProvider
     */
    public function testUpdateStatus(
        $inputCart,
        $inputStatus
    ): void {
        $cartStatusRepository = $this->app->make(CartStatusRepositoryInterface::class);
        $sut = $this->app->make(CartRepositoryInterface::class);

        $activeStatus = CartStatus::factory()->create(['name' => 'active']);
        $expiredStatus = CartStatus::factory()->create(['name' => 'expired']);

        $cart1 = Cart::factory()->create(['cart_status_id' => $activeStatus->uuid]);
        $cart2 = Cart::factory()->create(['cart_status_id' => $activeStatus->uuid]);

        $inputCart = $inputCart === 'cart1_uuid' ? $cart1->uuid : $sut->getCart($cart1->uuid);
        $inputStatus = $inputStatus === 'expired_uuid' ? $expiredStatus->uuid : $cartStatusRepository->getByName('expired');

        $sut->updateStatus($inputCart, $inputStatus);

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart1->uuid,
            'cart_status_id' => $expiredStatus->uuid,
        ]);

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart2->uuid,
            'cart_status_id' => $activeStatus->uuid,
        ]);
    }

    public static function updateStatusDataProvider(): array
    {
        return [
            ['cart1_uuid', 'expired_uuid'],
            ['cart1_dto', 'expired_uuid'],
            ['cart1_uuid', 'expired_status_dto'],
        ];
    }

    /**
     * @dataProvider getFinishedUserCartsDataProvider
     */
    public function testGetFinishedUserCarts($userInput, int $expectedCartCount): void
    {
        $user = User::factory()->create();
        $finishedStatus = CartStatus::factory()->create(['name' => CartStatus::FINISHED]);
        $otherStatus = CartStatus::factory()->create(['name' => 'other']);
        $soldSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::SOLD]);
        $ticketType = TicketType::factory()->create();

        $finishedCarts = Cart::factory()->count(2)->create([
            'user_id' => $user->uuid,
            'cart_status_id' => $finishedStatus->uuid,
        ]);

        $exhibition = Exhibition::factory()->create();

        foreach ($finishedCarts as $finishedCart) {
            $ticket = Ticket::factory()->create([
                'cart_id' => $finishedCart->uuid,
                'exhibition_id' => $exhibition->uuid,
                'ticket_type_id' => $ticketType->uuid,
            ]);

            ExhibitionSeat::factory()->create([
                'theater_room_seat_id' => $ticket->theater_room_seat_id,
                'exhibition_id' => $ticket->exhibition_id,
                'seat_status_id' => $soldSeatStatus->uuid,
            ]);
        }

        $otherCart = Cart::factory()->create([
            'user_id' => $user->uuid,
            'cart_status_id' => $otherStatus->uuid,
        ]);

        $otherTicket = Ticket::factory()->create([
            'cart_id' => $otherCart->uuid,
            'exhibition_id' => $exhibition->uuid,
        ]);

        ExhibitionSeat::factory()->create([
            'theater_room_seat_id' => $otherTicket->theater_room_seat_id,
            'exhibition_id' => $otherTicket->exhibition_id,
            'seat_status_id' => $soldSeatStatus->uuid,
        ]);

        ExhibitionTicketType::factory()->create([
            'exhibition_id' => $otherTicket->exhibition_id,
            'ticket_type_id' => $ticketType->uuid
        ]);

        $userInput = $userInput === 'user_uuid' ? $user->uuid : UserDTO::fromArray($user->getAttributes());

        $cartRepository = $this->app->make(CartRepositoryInterface::class);
        $finishedUserCarts = $cartRepository->getFinishedUserCarts($userInput);

        $this->assertCount($expectedCartCount, $finishedUserCarts);

        foreach ($finishedCarts as $finishedCart) {
            $this->assertTrue($finishedUserCarts->contains('uuid', $finishedCart->uuid));
        }
    }

    public static function getFinishedUserCartsDataProvider(): array
    {
        return [
            ['user_uuid', 2],
            ['user_dto', 2],
        ];
    }
}
