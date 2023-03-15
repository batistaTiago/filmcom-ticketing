<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\FilmDTO;
use Illuminate\Support\Collection;

interface FilmRepositoryInterface
{
    public function create(FilmDTO $film): void;

    public function findByUuid(string $uuid): FilmDTO;
    public function getByUuids(Collection $uuids): Collection;
    public function getWithFilters(array $filters): array;
}
