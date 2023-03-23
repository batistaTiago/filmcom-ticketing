<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomSeatStatusDTO
{
    public const ATTRIBUTES = ['uuid', 'name'];

    public function __construct(
        public readonly string $uuid,
        public string $name
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Name should not be empty');
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }
}
