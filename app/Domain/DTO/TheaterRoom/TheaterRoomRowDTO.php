<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomRowDTO
{
    public readonly string $uuid;
    public string $name;

    public const ATTRIBUTES = ['uuid', 'name', 'seats'];

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

    public static function fromArray($data): static
    {
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }
}
