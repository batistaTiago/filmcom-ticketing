<?php

namespace App\Domain\DTO;

use InvalidArgumentException;

class ExhibitionTicketTypeDTO
{
    public const ATTRIBUTES = [
        'uuid',
        'exhibition_id',
        'ticket_type_id',
        'price',
    ];

    public function __construct(
        public readonly string $uuid,
        public readonly string $exhibition_id,
        public readonly string $ticket_type_id,
        public int $price,
    ) {
        if ($this->price < 0) {
            throw new InvalidArgumentException('Price must be greater than zero.');
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }
}
