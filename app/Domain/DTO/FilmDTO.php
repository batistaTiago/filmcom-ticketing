<?php

namespace App\Domain\DTO;

use InvalidArgumentException;

class FilmDTO
{
    public readonly string $uuid;
    public string $name;
    public int $year;
    public int $duration;

    public const ATTRIBUTES = ['uuid', 'name', 'year', 'duration'];

    public function __construct(
        string $uuid,
        string $name,
        int $year,
        int $duration
    ) {
        if ($year < 0) {
            throw new InvalidArgumentException('Year must be greater than zero');
        }

        if ($duration < 0) {
            throw new InvalidArgumentException('Duration must be greater than zero');
        }

        if (empty($name)) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        $this->uuid = $uuid;
        $this->name = $name;
        $this->year = $year;
        $this->duration = $duration;
    }

    public static function fromArray(array $data): static
    {
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }
}
