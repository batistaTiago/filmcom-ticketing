<?php

namespace App\Domain\DTO;

use Carbon\Carbon;

class ExhibitionDTO
{
    public readonly string $uuid;
    public string $film_id;
    public string $theater_room_id;
    public Carbon $starts_at;
    public int $day_of_week;
    public bool $is_active;

    public const ATTRIBUTES = [
        'uuid',
        'film_id',
        'theater_room_id',
        'starts_at',
        'day_of_week',
        'is_active',
    ];

    public function __construct(
        string $uuid,
        string $film_id,
        string $theater_room_id,
        Carbon|string|int $starts_at,
        int $day_of_week,
        bool $is_active,
    ) {
        $this->uuid = $uuid;
        $this->film_id = $film_id;
        $this->theater_room_id = $theater_room_id;
        $this->starts_at = new Carbon($starts_at);
        $this->day_of_week = $day_of_week;
        $this->is_active = $is_active;
    }

    public static function fromArray($data): static
    {
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }
}
