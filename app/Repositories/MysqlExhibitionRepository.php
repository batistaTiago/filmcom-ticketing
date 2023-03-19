<?php

namespace App\Repositories;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\FilmDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
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

    public function findById(string|FilmDTO $input): ExhibitionDTO
    {
        $exhibition_id = $input instanceof FilmDTO ? $input->uuid : $input;
        $exhibition = Exhibition::query()->where('uuid', $exhibition_id)->first();

        if (empty($exhibition)) {
            throw new ResourceNotFoundException('Exhibition was not found: ' . $exhibition_id, 404);
        }

        return $exhibition->toDto();
    }

    /**
     * @return Collection<ExhibitionDTO>
     */
    public function getFilmExhibitions(string|FilmDTO $input): Collection
    {
        $film_id = $input instanceof FilmDTO ? $input->uuid : $input;

        return Exhibition::query()
            ->where(compact('film_id'))
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
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

    public function update(ExhibitionDTO $exhibition): void
    {
        Exhibition::where('uuid', $exhibition->uuid)->update((array) $exhibition);
    }
}
