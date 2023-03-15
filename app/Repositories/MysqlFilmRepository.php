<?php

namespace App\Repositories;

use App\Domain\DTO\FilmDTO;
use App\Domain\Repositories\FilmRepositoryInterface;
use App\Models\Film;
use Illuminate\Support\Collection;

class MysqlFilmRepository implements FilmRepositoryInterface
{
    private const AVAILABLE_FILTERS = ['name', 'year'];

    public function create(FilmDTO $film): void
    {
        Film::query()->create((array) $film);
    }

    public function getByUuids(Collection $uuids): Collection
    {
        return Film::query()
            ->whereIn('uuid', $uuids->toArray())
            ->get()
            ->map(fn (Film $film) => $film->toDTO());
    }

    public function getWithFilters(array $filters): array
    {
        $baseQuery = Film::query();

        foreach (self::AVAILABLE_FILTERS as $availableFilter) {
            if (isset($filters[$availableFilter])) {
                $baseQuery->where($availableFilter, $filters[$availableFilter]);
            }
        }

        $paginatedResult = $baseQuery->paginate(10)->appends(request()->query())->toArray();

        $paginatedResult['data'] = array_map(function ($item) {
            return FilmDTO::fromArray($item);
        }, $paginatedResult['data']);

        return $paginatedResult;
    }

    public function findByUuid(string $uuid): FilmDTO
    {
        return Film::query()
            ->where('uuid', $uuid)
            ->first()
            ->toDTO();
    }
}
