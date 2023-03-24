<?php

namespace App\Domain\DTO;

use InvalidArgumentException;

class TicketTypeDTO
{
    // TODO use NamedDTO
    public function __construct(public string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }
    }
}
