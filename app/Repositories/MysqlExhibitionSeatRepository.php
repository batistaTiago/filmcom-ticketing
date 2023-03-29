<?php

namespace App\Repositories;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\ExhibitionSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;
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

    public function changeSeatStatus(
        ExhibitionDTO|string $exhibition,
        TheaterRoomSeatDTO|string $seat,
        TheaterRoomSeatStatusDTO|string $status
    ): void
    {
        $theater_room_seat_id = $seat instanceof TheaterRoomSeatDTO ? $seat->uuid : $seat;
        $seat_status_id = $status instanceof TheaterRoomSeatStatusDTO ? $status->uuid : $status;

        ExhibitionSeat::query()->where([
            'exhibition_id' => $exhibition->uuid,
            'theater_room_seat_id' => $theater_room_seat_id,
        ])->update(compact('seat_status_id'));
    }

    public function changeSeatStatusBatch(
        string $exhibition_id,
        array $theater_room_seat_ids,
        string $seat_status_id
    ): void {
        ExhibitionSeat::query()
            ->where('exhibition_id', $exhibition_id)
            ->whereIn('theater_room_seat_id', $theater_room_seat_ids)
            ->update(compact('seat_status_id'));
    }
}
