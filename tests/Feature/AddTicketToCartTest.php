<?php

use App\Models\Exhibition;
use App\Models\ExhibitionSeat;
use App\Models\ExhibitionTicketType;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\SeatStatus;
use App\Models\TheaterRoomSeat;
use App\Models\TicketType;
use App\Models\User;
use Tests\TestCase;

class AddTicketToCartTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        $this->expiredCartStatus = CartStatus::factory()->create(['name' => CartStatus::EXPIRED]);

        $this->user = User::factory()->create();
        $this->ticketType = TicketType::factory()->create();
        $this->seat = TheaterRoomSeat::factory()->create();

        $this->exhibition = Exhibition::factory()->create([
            'theater_room_id' => $this->seat->row->room->uuid
        ]);

        $this->availableSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::AVAILABLE]);
        $this->unavailableSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::UNAVAILABLE]);
        $this->reservedSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::RESERVED]);
    }

    /** @test */
    public function should_not_allow_non_logged_users_to_add_tickets_to_a_cart()
    {
        $this->postJson(route('api.cart.add-ticket'))->assertUnauthorized();
    }

    /** @test */
    public function should_validate_the_existence_of_exhibition_seat_and_ticket_type_ids()
    {
        $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => fake()->uuid,
            'ticket_type_id' => fake()->uuid,
            'theater_room_seat_id' => fake()->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_validate_the_relation_between_the_selected_exhibition_and_ticket_type()
    {
        $this->populateExhibitionSeats();

        $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_validate_the_relation_between_the_selected_exhibition_seat_and_seat_status()
    {
        $this->populateExhibitionTicketTypes();

        ExhibitionSeat::factory()->create([
            'exhibition_id' => $this->exhibition->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'seat_status_id' => $this->unavailableSeatStatus->uuid,
        ]);

        $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
        ])->assertBadRequest();
    }

    /** @test */
    public function should_create_a_cart_for_the_logged_user_if_no_cart_id_is_provided()
    {
        $this->withoutExceptionHandling();
        $this->populateExhibitionTicketTypes();
        $this->populateExhibitionSeats();

        $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
        ])->assertOk();

        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->uuid
        ]);
    }

    /** @test */
    public function should_return_the_tickets_along_with_the_cart_info()
    {
        $this->populateExhibitionTicketTypes();
        $this->populateExhibitionSeats();

        $res = $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
        ])->assertJsonStructure([
            'cart_state' => [
                'uuid',
                'status',
                'user',
                'tickets',
            ]
        ])->decodeResponseJson();

        $this->assertArrayNotHasKey('password', $res['cart_state']['user']);
    }

    /** @test */
    public function should_create_a_new_cart_if_the_provided_cart_id_is_not_active()
    {
        $this->populateExhibitionTicketTypes();
        $this->populateExhibitionSeats();

        $cart = Cart::factory()->create([
            'user_id' => $this->user->uuid,
            'cart_status_id' => $this->expiredCartStatus->uuid
        ]);

        $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'cart_id' => $cart->uuid
        ])->assertOk();

        $this->assertDatabaseCount('carts', 2);
    }

    /** @test */
    public function should_change_the_availability_of_the_exhibition_seat_to_reserved_on_ticket_creation()
    {
        $this->withoutExceptionHandling();

        $this->populateExhibitionTicketTypes();
        $this->populateExhibitionSeats();

        $cart = Cart::factory()->create([
            'user_id' => $this->user->uuid,
            'cart_status_id' => $this->expiredCartStatus->uuid
        ]);

        $this->actingAs($this->user)->postJson(route('api.cart.add-ticket'), [
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'cart_id' => $cart->uuid
        ])->assertOk();

        $this->assertDatabaseMissing('exhibition_seats', [
            'exhibition_id' => $this->exhibition->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'seat_status_id' => $this->availableSeatStatus->uuid,
        ]);

        $this->assertDatabaseHas('exhibition_seats', [
            'exhibition_id' => $this->exhibition->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'seat_status_id' => $this->reservedSeatStatus->uuid,
        ]);
    }

    private function populateExhibitionTicketTypes(): void
    {
        ExhibitionTicketType::factory()->create([
            'exhibition_id' => $this->exhibition->uuid,
            'ticket_type_id' => $this->ticketType->uuid,
            'price' => fake()->numberBetween(1000, 5000)
        ]);
    }

    private function populateExhibitionSeats(): void
    {
        ExhibitionSeat::factory()->create([
            'exhibition_id' => $this->exhibition->uuid,
            'theater_room_seat_id' => $this->seat->uuid,
            'seat_status_id' => $this->availableSeatStatus->uuid,
        ]);
    }
}
