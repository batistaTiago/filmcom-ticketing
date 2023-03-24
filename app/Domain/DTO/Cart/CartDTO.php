<?php

namespace App\Domain\DTO\Cart;

use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\UserDTO;
use InvalidArgumentException;

class CartDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly UserDTO $user,
        public ?CartStatusDTO $status = null,
        public array $tickets = [],
    )
    {
        if (empty($uuid)) {
            throw new InvalidArgumentException('Uuid must not be empty');
        }

        foreach ($this->tickets as $ticket) {
            if (!($ticket instanceof TicketDTO)) {
                throw new InvalidArgumentException('Tickets must be an array of TicketDTOs');
            }
        }
    }
}
