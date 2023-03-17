<?php

namespace App\Domain\DTO;

use InvalidArgumentException;

class TicketTypeDTO
{
    public function __construct(public string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }
    }
}
