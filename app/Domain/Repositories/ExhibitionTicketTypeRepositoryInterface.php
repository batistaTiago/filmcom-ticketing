<?php

namespace App\Domain\Repositories;

use App\Models\ExhibitionTicketType;

interface ExhibitionTicketTypeRepositoryInterface
{
    // TODO create a DTO for this repository
    public function findExhibitionTicketType(string $exhibition_id, string $ticket_type_id): ExhibitionTicketType;
    public function exists(string $exhibition_id, string $ticket_type_id): bool;
}
