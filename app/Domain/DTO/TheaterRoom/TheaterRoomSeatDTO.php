<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomSeatDTO
{
    public readonly string $uuid;
    public string $name;
    public TheaterRoomSeatTypeDTO $type;
    public ?TheaterRoomSeatStatusDTO $status;

    public function __construct(
        string $uuid,
        string $name,
        TheaterRoomSeatTypeDTO $type,
        ?TheaterRoomSeatStatusDTO $status = null,
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }

        if (empty($uuid)) {
            throw new InvalidArgumentException('Uuid should not be empty');
        }

        $this->uuid = $uuid;
        $this->name = $name;
        $this->type = $type;
        $this->status = $status;
    }
}
