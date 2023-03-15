<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\TheaterDTO;

interface TheaterRepositoryInterface
{
    public function create(TheaterDTO $theater): void;
}
