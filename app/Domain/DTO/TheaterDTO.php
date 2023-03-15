<?php

namespace App\Domain\DTO;

use InvalidArgumentException;

class TheaterDTO
{
    public readonly string $uuid;
    public string $name;

    public function __construct(
        string $uuid,
        string $name,
    )
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        $this->uuid = $uuid;
        $this->name = $name;
    }

    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}
