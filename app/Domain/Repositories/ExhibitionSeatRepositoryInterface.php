<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\ExhibitionSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;

interface ExhibitionSeatRepositoryInterface
{
    public function findExhibitionSeat(string $exhibition_id, string $theater_room_seat_id): ExhibitionSeatDTO;
    public function exists(string $exhibition_id, string $theater_room_seat_id): bool;
    public function changeSeatStatus(
        ExhibitionDTO|string $exhibition,
        TheaterRoomSeatDTO|string $seat,
        TheaterRoomSeatStatusDTO|string $status
    ): void;

    public function changeSeatStatusBatch(
        string $exhibition_id,
        array $theater_room_seat_ids,
        string $seat_status_id
    ): void;
}
