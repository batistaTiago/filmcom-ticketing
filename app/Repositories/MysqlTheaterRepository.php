<?php

namespace App\Repositories;

use App\Domain\DTO\TheaterDTO;
use App\Domain\Repositories\TheaterRepositoryInterface;
use App\Models\Theater;

class MysqlTheaterRepository implements TheaterRepositoryInterface
{
    public function create(TheaterDTO $theater): void
    {
        Theater::query()->create((array) $theater);
    }
}
