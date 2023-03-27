<?php

namespace App\Domain\Repositories;

use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    public function findTicketsInCart(string $cartUuid): Collection;
    public function removeTicketFromCart(string $cart_id, string $ticket_id);
    public function addToCart(
        string $cart_id,
        string $exhibition_id,
        string $theater_room_seat_id,
        string $ticket_type_id,
    ): void;
}
