<?php

namespace App\Repositories;

use App\Domain\DTO\ExhibitionTicketTypeDTO;
use App\Domain\Repositories\ExhibitionTicketTypeRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\ExhibitionTicketType;

class MysqlExhibitionTicketTypeRepository implements ExhibitionTicketTypeRepositoryInterface
{
    public function findExhibitionTicketType(string $exhibition_id, string $ticket_type_id): ExhibitionTicketTypeDTO
    {
        $entry = ExhibitionTicketType::query()
            ->where(compact('exhibition_id', 'ticket_type_id'))
            ->first();

        if (!$entry) {
            throw new ResourceNotFoundException('Exhibition or ticket type not found');
        }

        return $entry->toDto();
    }

    public function exists(string $exhibition_id, string $ticket_type_id): bool
    {
        return ExhibitionTicketType::query()
            ->where(compact('exhibition_id', 'ticket_type_id'))
            ->exists();
    }
}
