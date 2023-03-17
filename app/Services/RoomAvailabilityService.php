<?php

namespace App\Services;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\FilmDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Repositories\FilmRepositoryInterface;
use App\Domain\Services\RoomAvailabilityServiceInterface;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;

class RoomAvailabilityService implements RoomAvailabilityServiceInterface
{
    public function __construct(
        private readonly ExhibitionRepositoryInterface $exhibitionRepository,
        private readonly FilmRepositoryInterface $filmRepository,
    ) { }

    public function validate(ExhibitionDTO $exhibition): void
    {
        $currentExhibitions = $this->getCurrentExhibitions($exhibition);

        $dailyRoomFilms = $this->getDailyRoomFilms($currentExhibitions);

        foreach ($currentExhibitions as $currentExhibition) {
            $currentExhibitionFilm = $dailyRoomFilms[$currentExhibition->film_id];
            $this->detectConflictBetweenExhibitions($exhibition, $currentExhibition, $currentExhibitionFilm);
        }
    }

    private function detectConflictBetweenExhibitions(
        ExhibitionDTO $newExhibition,
        ExhibitionDTO $currentExhibition,
        FilmDTO $currentExhibitionFilm
    ): void
    {
        $exhibitionStartsAt = Carbon::parse($newExhibition->starts_at);
        $exhibitionEndsAt = $exhibitionStartsAt->clone()->addMinutes($currentExhibitionFilm->duration);

        $dailyRoomExhibitionStartsAt = Carbon::parse($currentExhibition->starts_at);
        $dailyRoomExhibitionEndsAt = $dailyRoomExhibitionStartsAt->clone()->addMinutes($currentExhibitionFilm->duration);

        if (($dailyRoomExhibitionStartsAt <= $exhibitionStartsAt) && ($dailyRoomExhibitionEndsAt >= $exhibitionStartsAt)) {
            throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt",
                400);
        }

        if (($dailyRoomExhibitionStartsAt <= $exhibitionEndsAt) && ($dailyRoomExhibitionEndsAt >= $exhibitionEndsAt)) {
            throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt",
                400);
        }

        if (($exhibitionStartsAt <= $dailyRoomExhibitionStartsAt) && ($exhibitionEndsAt >= $dailyRoomExhibitionEndsAt)) {
            throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt",
                400);
        }
    }


    public function getCurrentExhibitions(ExhibitionDTO $exhibition): Collection
    {
        return $this->exhibitionRepository
            ->getDailyExhibitionsInRoom($exhibition->day_of_week, $exhibition->theater_room_id)
            ->filter(fn($currentExhibition) => $currentExhibition->uuid !== $exhibition->uuid);
    }

    public function getDailyRoomFilms(Collection $currentExhibitions)
    {
        return $this->filmRepository
            ->getByUuids($currentExhibitions->pluck('film_id'))
            ->groupBy('uuid')
            ->map(fn(Collection $grouped) => $grouped->first());
    }
}
