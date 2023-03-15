<?php

namespace App\Repositories;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;
use App\Domain\Repositories\TheaterRoomRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
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
}
