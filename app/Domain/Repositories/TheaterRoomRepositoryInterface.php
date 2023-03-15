<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;

interface TheaterRoomRepositoryInterface
{
    public function findRoomById(string $uuid): TheaterRoomDTO;
}
