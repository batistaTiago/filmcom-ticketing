<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\FilmDTO;
use Illuminate\Support\Collection;

interface ExhibitionRepositoryInterface
{
    public function create(ExhibitionDTO $exhibition): void;
    public function findById(FilmDTO|string $input): ExhibitionDTO;
    public function getFilmExhibitions(FilmDTO|string $input): Collection;
    public function getDailyExhibitionsInRoom(int $day_of_week, string $theater_room_id);
    public function update(ExhibitionDTO $exhibition): void;
}
