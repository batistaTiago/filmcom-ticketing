<?php

namespace Tests\Infrastructure\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
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
    private readonly CartRepositoryInterface $sut;
    private readonly CartStatus $activeStatus;
    private readonly CartStatus $expiredStatus;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->app->make(CartRepositoryInterface::class);
        $this->activeStatus = CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        $this->expiredStatus = CartStatus::factory()->create(['name' => CartStatus::EXPIRED]);
    }

    /**
     * @test
     * @dataProvider updateStatusDataProvider
     */
    public function should_update_a_single_cart_status_and_save_a_cart_history_record(
        $inputCart,
        $inputStatus
    ): void {
        $cartStatusRepository = $this->app->make(CartStatusRepositoryInterface::class);

        $cart1 = Cart::factory()->create(['cart_status_id' => $this->activeStatus->uuid]);
        $cart2 = Cart::factory()->create(['cart_status_id' => $this->activeStatus->uuid]);

        $inputCart = $inputCart === 'cart1_uuid' ? $cart1->uuid : $this->sut->getCart($cart1->uuid);
        $inputStatus = $inputStatus === 'expired_uuid' ? $this->expiredStatus->uuid : $cartStatusRepository->getByName('expired');

        $this->sut->updateStatus($inputCart, $inputStatus);

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart1->uuid,
            'cart_status_id' => $this->expiredStatus->uuid,
        ]);

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart2->uuid,
            'cart_status_id' => $this->activeStatus->uuid,
        ]);

        $this->assertDatabaseHas('cart_history', [
            'cart_id' => $cart1->uuid,
            'cart_status_id' => $this->expiredStatus->uuid,
        ]);

        $this->assertDatabaseMissing('cart_history', [
            'cart_id' => $cart2->uuid,
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
     * @test
     * @dataProvider getFinishedUserCartsDataProvider
     */
    public function should_fetch_all_finished_carts_from_the_database($userInput, int $expectedCartCount): void
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

        $finishedUserCarts = $this->sut->getFinishedUserCarts($userInput);

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

    /** @test */
    public function should_create_a_new_cart_when_cart_uuid_is_empty()
    {
        $user = User::factory()->create();

        $cart = $this->sut->getOrCreateActiveCart($user->uuid);

        $this->assertInstanceOf(CartDTO::class, $cart);
        $this->assertDatabaseHas('carts', ['user_id' => $user->uuid]);
    }

    /** @test */
    public function should_insert_a_cart_status_record_when_cart_uuid_is_empty()
    {
        $user = User::factory()->create();

        $cart = $this->sut->getOrCreateActiveCart($user->uuid);

        $this->assertInstanceOf(CartDTO::class, $cart);
        $this->assertDatabaseHas('carts', ['user_id' => $user->uuid]);
        $this->assertDatabaseHas('cart_history', ['cart_id' => $cart->uuid]);
    }

    /** @test */
    public function should_return_existing_cart_when_cart_uuid_is_not_empty_and_cart_exists()
    {
        $existingCart = Cart::factory()->create(['cart_status_id' => $this->activeStatus->uuid]);

        $cart = $this->sut->getOrCreateActiveCart($existingCart->user_id, $existingCart->uuid);

        $this->assertInstanceOf(CartDTO::class, $cart);
        $this->assertEquals($existingCart->uuid, $cart->uuid);
    }

    /** @test */
    public function should_insert_a_cart_status_record_when_cart_uuid_is_not_empty_and_cart_does_not_exist()
    {
        $user = User::factory()->create();
        $cart = $this->sut->getOrCreateActiveCart($user->uuid, fake()->uuid);

        $this->assertInstanceOf(CartDTO::class, $cart);
        $this->assertDatabaseHas('carts', ['user_id' => $user->uuid]);
        $this->assertDatabaseHas('cart_history', ['cart_id' => $cart->uuid]);
    }

    /** @test */
    public function should_create_a_new_cart_when_cart_uuid_is_not_empty_and_cart_exists_but_is_not_active()
    {
        $existingCart = Cart::factory()->create(['cart_status_id' => $this->expiredStatus->uuid]);

        $user = User::factory()->create();
        $cart = $this->sut->getOrCreateActiveCart($user->uuid, $existingCart->uuid);

        $this->assertInstanceOf(CartDTO::class, $cart);
        $this->assertDatabaseHas('carts', ['user_id' => $user->uuid]);
    }

    /** @test */
    public function should_insert_a_cart_status_record_when_cart_uuid_is_not_empty_and_cart_exists_but_is_not_active()
    {
        $existingCart = Cart::factory()->create(['cart_status_id' => $this->expiredStatus->uuid]);

        $user = User::factory()->create();
        $cart = $this->sut->getOrCreateActiveCart($user->uuid, $existingCart->uuid);

        $this->assertInstanceOf(CartDTO::class, $cart);
        $this->assertDatabaseHas('carts', ['user_id' => $user->uuid]);
        $this->assertDatabaseHas('cart_history', ['cart_id' => $cart->uuid]);
    }
}
