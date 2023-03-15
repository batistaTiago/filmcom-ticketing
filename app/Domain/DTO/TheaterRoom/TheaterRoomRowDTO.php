<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomRowDTO
{
    public readonly string $uuid;
    public string $name;

    /** @var TheaterRoomSeatDTO[] */
    public array $seats;

    public function __construct(
        string $uuid,
        string $name,
        array $seats = [],
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }

        if (empty($uuid)) {
            throw new InvalidArgumentException('Uuid should not be empty');
        }

        $this->uuid = $uuid;
        $this->name = $name;
        $this->seats = $seats;
    }
}
