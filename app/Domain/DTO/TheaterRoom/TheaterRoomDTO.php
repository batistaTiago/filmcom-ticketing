<?php

namespace App\Domain\DTO\TheaterRoom;

use InvalidArgumentException;

class TheaterRoomDTO
{
    public string $uuid;
    public string $name;

    /** @var TheaterRoomRowDTO[] */
    public array $rows;

    public const ATTRIBUTES = [
        'uuid',
        'name',
        'theater_id'
    ];

    public function __construct(
        string $uuid,
        string $name,
        string $theater_id,
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
        $this->theater_id = $theater_id;
        $this->rows = $rows;
    }

    public static function fromArray(array $data): static
    {
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }
}
