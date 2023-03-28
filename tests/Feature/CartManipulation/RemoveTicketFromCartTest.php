<?php

namespace Tests\Feature\CartManipulation;

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
use Carbon\Carbon;
use Tests\TestCase;

class RemoveTicketFromCartTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $fakeTestNow = Carbon::create('2023', '03', '27', '23', '00');
        Carbon::setTestNow($fakeTestNow);

        $this->activeCartStatus = CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        $this->expiredCartStatus = CartStatus::factory()->create(['name' => CartStatus::EXPIRED]);
        $this->availableSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::AVAILABLE]);
        $this->unavailableSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::UNAVAILABLE]);
        $this->reservedSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::RESERVED]);

        $this->user = User::factory()->create();
        $this->ticketType = TicketType::factory()->create(['name' => TicketType::REGULAR]);
        $this->seat = TheaterRoomSeat::factory()->create();

        $this->exhibition = Exhibition::factory()->create([
            'theater_room_id' => $this->seat->row->room->uuid
        ]);

        ExhibitionTicketType::factory()->create([
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'price' => fake()->numberBetween(1000, 5000)
        ]);

        ExhibitionSeat::factory()->create([
            'exhibition_id' => $this->exhibition->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'seat_status_id' => $this->availableSeatStatus->uuid,
        ]);

        $this->cart = Cart::factory()->create([
            'cart_status_id' => $this->activeCartStatus->uuid
        ]);
        $this->ticket = Ticket::factory()->create([
            'cart_id' => $this->cart->uuid,
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid
        ]);
    }

    /** @test */
    public function should_not_allow_non_logged_users_to_remove_tickets_from_a_cart()
    {
        $this->postJson(route('api.cart.remove-ticket'))->assertUnauthorized();
    }

    /** @test */
    public function should_validate_the_existence_of_ticket_and_cart_ids()
    {
        $this->actingAs($this->user)->postJson(route('api.cart.remove-ticket'), [
            'ticket_id' => fake()->uuid,
            'cart_id' => fake()->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_validate_the_relation_between_the_selected_ticket_and_cart()
    {
        $secondCart = Cart::factory()->create();
        $this->actingAs($this->user)->postJson(route('api.cart.remove-ticket'), [
            'ticket_id' => $this->ticket->uuid,
            'cart_id' => $secondCart->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_update_the_exhibition_seat_status_and_delete_the_ticket()
    {
        $this->actingAs($this->user)->postJson(route('api.cart.remove-ticket'), [
            'ticket_id' => $this->ticket->uuid,
            'cart_id' => $this->cart->uuid,
        ])->assertOk();

        $updatedExhibitionSeat = ExhibitionSeat::query()->where([
            'exhibition_id' => $this->ticket->exhibition_id,
            'theater_room_seat_id' => $this->ticket->theater_room_seat_id,
        ])->first();

        $this->assertEquals($this->availableSeatStatus->uuid, $updatedExhibitionSeat->seat_status_id);

        $deletedTicket = Ticket::query()->where('uuid', $this->ticket->uuid)->first();
        $this->assertNull($deletedTicket);
    }

    /** @test */
    public function should_propagate_the_update_to_the_cart()
    {
        $fakeTestNow = Carbon::create('2023', '03', '28', '00', '00');
        Carbon::setTestNow($fakeTestNow);
        $this->actingAs($this->user)->postJson(route('api.cart.remove-ticket'), [
            'ticket_id' => $this->ticket->uuid,
            'cart_id' => $this->cart->uuid,
        ])->assertOk();
        $this->assertEquals($fakeTestNow->toDateTimeString(), $this->cart->fresh()->updated_at->toDateTimeString());
    }
}
