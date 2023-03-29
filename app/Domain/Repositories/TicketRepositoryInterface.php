<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;
use App\Domain\DTO\TicketDTO;
use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    public function findTicketsInCart(string $cartUuid): Collection;
    public function removeTicketFromCart(string $cart_id, string $ticket_id);
    public function createTicketInCart(
        string $cart_id,
        string $exhibition_id,
        string $theater_room_seat_id,
        string $ticket_type_id,
    ): void;

    public function changeStatus(
        TicketDTO $ticket,
        TheaterRoomSeatStatusDTO|string $status
    ): void;
}
