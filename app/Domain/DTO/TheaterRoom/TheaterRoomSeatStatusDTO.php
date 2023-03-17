<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomSeatStatusDTO
{
    public function __construct(public string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }
    }
}
