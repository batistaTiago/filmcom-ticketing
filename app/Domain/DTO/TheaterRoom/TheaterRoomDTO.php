<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomDTO
{
    public string $uuid;
    public string $name;

    /** @var TheaterRoomRowDTO[] */
    public array $rows;

    public function __construct(
        string $uuid,
        string $name,
        array $rows = [],
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }

        if (empty($uuid)) {
            throw new InvalidArgumentException('Uuid should not be empty');
        }

        $this->uuid = $uuid;
        $this->name = $name;
        $this->rows = $rows;
    }
}
