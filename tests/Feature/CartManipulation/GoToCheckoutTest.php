<?php

namespace CartManipulation;

use App\Jobs\Checkout\IssueTicketsJob;
use App\Jobs\Checkout\ProcessCartPaymentJob;
use App\Jobs\Checkout\SendPurchaseCompleteEmailJob;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\Exhibition;
use App\Models\ExhibitionSeat;
use App\Models\ExhibitionTicketType;
use App\Models\SeatStatus;
use App\Models\TheaterRoomSeat;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class GoToCheckoutTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $activeCartStatus = CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        CartStatus::factory()->create(['name' => CartStatus::EXPIRED]);
        CartStatus::factory()->create(['name' => CartStatus::AWAITING_PAYMENT]);
        CartStatus::factory()->create(['name' => CartStatus::FINISHED]);

        $availableSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::AVAILABLE]);
        SeatStatus::factory()->create(['name' => SeatStatus::UNAVAILABLE]);
        SeatStatus::factory()->create(['name' => SeatStatus::RESERVED]);

        $this->user = User::factory()->create();
        $ticketType = TicketType::factory()->create(['name' => TicketType::REGULAR]);
        $seat = TheaterRoomSeat::factory()->create();

        $exhibition = Exhibition::factory()->create([
            'theater_room_id' => $seat->row->room->uuid
        ]);

        ExhibitionTicketType::factory()->create([
            'exhibition_id' => $exhibition->uuid,
            'ticket_type_id' => $ticketType->uuid,
            'price' => fake()->numberBetween(1000, 5000)
        ]);

        ExhibitionSeat::factory()->create([
            'exhibition_id' => $exhibition->uuid,
            'theater_room_seat_id' => $seat->uuid,
            'seat_status_id' => $availableSeatStatus->uuid,
        ]);

        $this->cart = Cart::factory()->create([
            'cart_status_id' => $activeCartStatus->uuid,
            'user_id' => $this->user->uuid,
        ]);

        $this->ticket = Ticket::factory()->create([
            'cart_id' => $this->cart->uuid,
            'exhibition_id' => $exhibition->uuid,
            'ticket_type_id' => $ticketType->uuid,
            'theater_room_seat_id' => $seat->uuid
        ]);
    }

    /** @test */
    public function should_not_allow_non_logged_users_to_go_to_checkout()
    {
        $this->postJson(route('api.cart.go-to-checkout'))->assertUnauthorized();
    }

    /** @test */
    public function should_validate_the_existence_of_the_cart_id()
    {
        $this->actingAs($this->user)->postJson(route('api.cart.go-to-checkout'), [
            'cart_id' => fake()->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_validate_the_relation_between_the_selected_cart_and_the_authenticated_user()
    {
        $secondCart = Cart::factory()->create();
        $this->actingAs($this->user)->postJson(route('api.cart.go-to-checkout'), [
            'cart_id' => $secondCart->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_update_the_cart_to_awaiting_payment_status()
    {
        Bus::fake();

        $this->actingAs($this->user)->postJson(route('api.cart.go-to-checkout'), [
            'cart_id' => $this->cart->uuid,
        ])->assertOk();

        $this->assertEquals(CartStatus::AWAITING_PAYMENT, $this->cart->fresh()->status->name);

        Bus::assertChained([
            ProcessCartPaymentJob::class,
            IssueTicketsJob::class,
            SendPurchaseCompleteEmailJob::class,
        ]);
    }

    /** @test */
    public function should_update_the_cart_to_finished_status_after_job_execution()
    {
        $soldSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::SOLD]);

        $this->actingAs($this->user)->postJson(route('api.cart.go-to-checkout'), [
            'cart_id' => $this->cart->uuid,
        ])->assertOk();

        $this->assertEquals(CartStatus::FINISHED, $this->cart->fresh()->status->name);

        $this->assertDatabaseCount('exhibition_seats', 1);
        $this->assertDatabaseHas('exhibition_seats', [
            'exhibition_id' => $this->ticket->exhibition_id,
            'theater_room_seat_id' => $this->ticket->theater_room_seat_id,
            'seat_status_id' => $soldSeatStatus->uuid
        ]);
    }
}
