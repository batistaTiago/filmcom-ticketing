<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\ExhibitionSeatDTO;

interface ExhibitionSeatRepositoryInterface
{
    public function findExhibitionSeat(string $exhibition_id, string $theater_room_seat_id): ExhibitionSeatDTO;
    public function exists(string $exhibition_id, string $theater_room_seat_id): bool;
}
