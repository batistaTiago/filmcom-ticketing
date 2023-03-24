<?php

namespace App\Repositories;

use App\Domain\DTO\ExhibitionSeatDTO;
use App\Domain\Repositories\ExhibitionSeatRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\ExhibitionSeat;

class MysqlExhibitionSeatRepository implements ExhibitionSeatRepositoryInterface
{

    public function findExhibitionSeat(string $exhibition_id, string $theater_room_seat_id): ExhibitionSeatDTO
    {
        $entry = ExhibitionSeat::query()->with('seat_status')->where([
            'exhibition_id' => $exhibition_id,
            'theater_room_seat_id' => $theater_room_seat_id,
        ])->first();

        if (!$entry) {
            throw new ResourceNotFoundException('Seat info not found for this exhibition.');
        }

        return $entry->toDto();
    }

    public function exists(string $exhibition_id, string $theater_room_seat_id): bool
    {
        return ExhibitionSeat::query()->where([
            'exhibition_id' => $exhibition_id,
            'theater_room_seat_id' => $theater_room_seat_id,
        ])->exists();
    }
}
