<?php

namespace App\Domain\DTO\Cart;

use App\Domain\DTO\CartStatusDTO;
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
        foreach ($this->tickets as $ticket) {
            if ($ticket instanceof TicketDTO) {
                throw new InvalidArgumentException();
            }
        }
    }
}
