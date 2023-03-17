<?php

namespace App\Repositories;

use App\Domain\Repositories\TicketTypeRepositoryInterface;
use App\Models\ExhibitionTicketType;
use App\Models\TicketType;
use Illuminate\Support\Collection;

class MysqlTicketTypeRepository implements TicketTypeRepositoryInterface
{
    public function getTicketTypes(): Collection
    {
        return TicketType::query()
            ->get()
            ->map(fn (TicketType $ticketType) => $ticketType->toDto());

    }

    public function getExhibitionTicketTypes(string $exhibition_id): Collection
    {
       return ExhibitionTicketType::query()
           ->with('ticket_type')
           ->where(compact('exhibition_id'))
           ->get()
           ->pluck('ticket_type')
           ->map(fn (TicketType $ticketType) => $ticketType->toDto());
    }
}
