<?php

namespace App\Repositories;

use App\Domain\Repositories\ExhibitionTicketTypeRepositoryInterface;
use App\Models\ExhibitionTicketType;

class MysqlExhibitionTicketTypeRepository implements ExhibitionTicketTypeRepositoryInterface
{
    public function findExhibitionTicketType(string $exhibition_id, string $ticket_type_id): ExhibitionTicketType
    {
        return ExhibitionTicketType::query()
            ->where(compact('exhibition_id', 'ticket_type_id'))
            ->first();
    }

    public function exists(string $exhibition_id, string $ticket_type_id): bool
    {
        return ExhibitionTicketType::query()
            ->where(compact('exhibition_id', 'ticket_type_id'))
            ->exists();
    }
}
