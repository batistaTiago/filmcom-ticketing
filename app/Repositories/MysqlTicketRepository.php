<?php

namespace App\Repositories;

use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\Repositories\TicketRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Cart;
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Models\Ticket;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MysqlTicketRepository implements TicketRepositoryInterface
{
    public function findTicketsInCart(string $cartUuid): Collection
    {
        $relations = ['type', 'seat.row', 'seat.type', 'seat.exhibition_seats.seat_status', 'exhibition', 'exhibition_ticket_types'];
        $tickets = Ticket::query()
            ->with($relations)
            ->where('cart_id', $cartUuid)
            ->get();

        return $tickets->map(function (Ticket $ticket) {
            return $ticket->prepareToDto()->toDto();
        });
    }

    public function createTicketInCart(
        string $cart_id,
        string $exhibition_id,
        string $theater_room_seat_id,
        string $ticket_type_id,
    ): void
    {
        $ticket = Ticket::query()->firstWhere(compact('exhibition_id', 'theater_room_seat_id'));

        if ($ticket) {
            throw new DomainException('This seat has already been taken. Please try a different one.');
        }

        $uuid = Str::orderedUuid()->toString();

        Ticket::query()->create(
            compact(
                'uuid',
                'exhibition_id',
                'theater_room_seat_id',
                'ticket_type_id',
                'cart_id'
            )
        );

        Cart::query()->update(['updated_at' => now()]);
    }

    public function removeTicketFromCart(string $cart_id, string $ticket_id)
    {
        $ticket = Ticket::query()->where('uuid', $ticket_id)->first();

        if (empty($ticket)) {
            throw new ResourceNotFoundException('Ticket not found');
        }

        if ($ticket->cart_id != $cart_id) {
            throw new ResourceNotFoundException('Ticket does not belong to this cart');
        }

        ExhibitionSeat::query()->where([
            'exhibition_id' => $ticket->exhibition_id,
            'theater_room_seat_id' => $ticket->theater_room_seat_id,
        ])->update([
            'seat_status_id' => SeatStatus::query()->firstWhere(['name' => SeatStatus::AVAILABLE])->uuid
        ]);

        $ticket->delete();

        Cart::query()->update(['updated_at' => now()]);
    }

    public function changeStatus(TicketDTO $ticket, TheaterRoomSeatStatusDTO|string $status): void
    {

    }
}
