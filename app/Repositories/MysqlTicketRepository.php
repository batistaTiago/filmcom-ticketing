<?php

namespace App\Repositories;

use App\Domain\Repositories\TicketRepositoryInterface;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MysqlTicketRepository implements TicketRepositoryInterface
{
    public function findTicketsInCart(string $cartUuid): Collection
    {
        $relations = ['type', 'seat.type', 'seat.exhibition_seats.seat_status', 'exhibition', 'exhibition_ticket_types'];
        $tickets = Ticket::query()
            ->with($relations)
            ->where('cart_id', $cartUuid)
            ->get();

        return $tickets->map(function (Ticket $ticket) {

            $ticket->exhibition_ticket_type = $ticket->exhibition_ticket_types
                ->where('exhibition_id', $ticket->exhibition_id)
                ->first();

            $ticket->seat->exhibition_seat = $ticket->seat->exhibition_seats
                ->where('exhibition_id', $ticket->exhibition_id)
                ->first();

            unset($ticket->exhibition_ticket_types);
            unset($ticket->exhibition_seats);

            return $ticket->toDto();
        });
    }
}
