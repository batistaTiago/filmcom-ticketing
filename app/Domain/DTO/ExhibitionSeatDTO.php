<?php

namespace App\Domain\DTO;

use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;

class ExhibitionSeatDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $exhibition_id,
        public readonly string $theater_room_seat_id,
        public ?TheaterRoomSeatStatusDTO $seat_status = null,
    ) { }
}
