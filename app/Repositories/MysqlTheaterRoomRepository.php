<?php

namespace App\Repositories;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;
use App\Domain\Repositories\TheaterRoomRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Exhibition;
use App\Models\TheaterRoom;

class MysqlTheaterRoomRepository implements TheaterRoomRepositoryInterface
{
    // TODO: segundo parametro (ou decorator) para customizar o throw do error retornando null
    public function findRoomById(string $uuid): TheaterRoomDTO
    {
        $entry = TheaterRoom::query()->with('rows.seats.type')->where(compact('uuid'))->first();

        if (!$entry) {
            throw new ResourceNotFoundException("Theater room was not found: $uuid", 404);
        }

        return $entry->toDTO();
    }

    public function findRoomAvailability(string $exhibitionUuid): mixed
    {
        $exhibition = Exhibition::query()->where('uuid', $exhibitionUuid)->first();

        $entry = TheaterRoom::query()
            ->with('rows.seats.type')
            ->with([
                'rows.seats.exhibition_seats' => function ($builder) use ($exhibitionUuid) {
                    $builder->with('seat_status')->where('exhibition_id', $exhibitionUuid);
                }
            ])
            ->where(['uuid' => $exhibition->theater_room_id])
            ->first();

        if (!$entry) {
            throw new ResourceNotFoundException("Theater room was not found: $exhibition->theater_room_id", 404);
        }

        return $entry->toDTO();
    }

    public function create(TheaterRoomDTO $dto): void
    {
        TheaterRoom::query()->create((array) $dto);
    }
}
