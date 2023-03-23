<?php

namespace App\Domain\Repositories;

use App\Models\ExhibitionSeat;

interface ExhibitionSeatRepositoryInterface
{
    // TODO create a DTO for this repository
    public function findExhibitionSeat(string $exhibition_id, string $theater_room_seat_id): ExhibitionSeat;
    public function exists(string $exhibition_id, string $theater_room_seat_id): bool;
}
