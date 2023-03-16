<?php

namespace App\Repositories;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\FilmDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Models\Exhibition;
use Illuminate\Support\Collection;
use Laravel\Octane\Exceptions\DdException;

class MysqlExhibitionRepository implements ExhibitionRepositoryInterface
{
    public function create(ExhibitionDTO $exhibition): void
    {
        $data = (array) $exhibition;
        $data['starts_at'] = (string) $exhibition->starts_at;
        Exhibition::query()->create($data);
    }

    /**
     * @return Collection<ExhibitionDTO>
     */
    public function getFilmExhibitions(string|FilmDTO $input): Collection
    {
        $film_id = $input instanceof FilmDTO ? $input->uuid : $input;

        return Exhibition::query()
            ->where(compact('film_id'))
            ->get()
            ->map(fn (Exhibition $exhibition) => $exhibition->toDTO());
    }

    /**
     * @return Collection<ExhibitionDTO>
     */
    public function getDailyExhibitionsInRoom(int $day_of_week, string $theater_room_id): Collection
    {
        return Exhibition::query()
            ->where(compact('day_of_week', 'theater_room_id'))
            ->where('is_active', true)
            ->get()
            ->map(fn (Exhibition $exhibition) => $exhibition->toDTO());
    }
}
