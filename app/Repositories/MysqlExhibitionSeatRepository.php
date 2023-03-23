<?php

namespace App\Repositories;

use App\Domain\Repositories\ExhibitionSeatRepositoryInterface;
use App\Models\ExhibitionSeat;

class MysqlExhibitionSeatRepository implements ExhibitionSeatRepositoryInterface
{

    public function findExhibitionSeat(string $exhibition_id, string $theater_room_seat_id): ExhibitionSeat
    {
        return ExhibitionSeat::query()->with('seat_status')->where([
            'exhibition_id' => $exhibition_id,
            'theater_room_seat_id' => $theater_room_seat_id,
        ])->first();
    }

    public function exists(string $exhibition_id, string $theater_room_seat_id): bool
    {
        return ExhibitionSeat::query()->where([
            'exhibition_id' => $exhibition_id,
            'theater_room_seat_id' => $theater_room_seat_id,
        ])->exists();
    }
}
