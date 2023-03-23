<?php

namespace App\Domain\DTO;

use InvalidArgumentException;

class ExhibitionTicketTypeDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $exhibition_id,
        public readonly string $ticket_type_id,
        public int $price,
    ) {
        if ($price < 0) {
            throw new InvalidArgumentException('Price must be greater than zero.');
        }
    }
}
