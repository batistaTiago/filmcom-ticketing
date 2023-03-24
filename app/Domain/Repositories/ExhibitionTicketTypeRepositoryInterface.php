<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\ExhibitionTicketTypeDTO;

interface ExhibitionTicketTypeRepositoryInterface
{
    public function findExhibitionTicketType(string $exhibition_id, string $ticket_type_id): ExhibitionTicketTypeDTO;
    public function exists(string $exhibition_id, string $ticket_type_id): bool;
}
