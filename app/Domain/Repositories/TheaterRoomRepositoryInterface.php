<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;

interface TheaterRoomRepositoryInterface
{
    public function create(TheaterRoomDTO $dto): void;
    public function findRoomById(string $uuid): TheaterRoomDTO;
    public function findRoomAvailability(string $exhibitionUuid): mixed;
}
