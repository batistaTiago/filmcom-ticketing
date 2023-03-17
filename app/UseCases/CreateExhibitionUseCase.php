<?php

namespace App\UseCases;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\FilmDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Repositories\FilmRepositoryInterface;
use App\Jobs\CreateExhibitionSeatAvailabilityJob;
use Carbon\Carbon;
use DomainException;

class CreateExhibitionUseCase
{
    public function __construct(
        private readonly ExhibitionRepositoryInterface $exhibitionRepository,
        private readonly FilmRepositoryInterface $filmRepository,
    )
    { }

    public function execute(array $data): ExhibitionDTO
    {
        $newExhibition = ExhibitionDTO::fromArray($data);
        $this->validateRoomAvailability($newExhibition);
        $this->exhibitionRepository->create($newExhibition);

        // TODO move to a job dispatcher interface
        CreateExhibitionSeatAvailabilityJob::dispatch($newExhibition);



        return $newExhibition;
    }

    private function validateRoomAvailability(ExhibitionDTO $newExhibition): void
    {
        $currentExhibitions = $this->exhibitionRepository
            ->getDailyExhibitionsInRoom($newExhibition->day_of_week, $newExhibition->theater_room_id);

        $dailyRoomFilms = $this->filmRepository
            ->getByUuids($currentExhibitions->pluck('film_id'))
            ->groupBy('uuid');

        foreach ($currentExhibitions as $currentExhibition) {
            $currentExhibitionFilm = $dailyRoomFilms[$currentExhibition->film_id]->first();
            $this->detectConflictBetweenExhibitions($newExhibition, $currentExhibition, $currentExhibitionFilm);
        }
    }

    private function detectConflictBetweenExhibitions(
        ExhibitionDTO $newExhibition,
        ExhibitionDTO $currentExhibition,
        FilmDTO $currentExhibitionFilm
    ): void {
        $exhibitionStartsAt = Carbon::parse($newExhibition->starts_at);
        $exhibitionEndsAt = $exhibitionStartsAt->clone()->addMinutes($currentExhibitionFilm->duration);

        $dailyRoomExhibitionStartsAt = Carbon::parse($currentExhibition->starts_at);
        $dailyRoomExhibitionEndsAt = $dailyRoomExhibitionStartsAt->clone()->addMinutes($currentExhibitionFilm->duration);

        if (($dailyRoomExhibitionStartsAt <= $exhibitionStartsAt) && ($dailyRoomExhibitionEndsAt >= $exhibitionStartsAt)) {
            throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt", 400);
        }

        if (($dailyRoomExhibitionStartsAt <= $exhibitionEndsAt) && ($dailyRoomExhibitionEndsAt >= $exhibitionEndsAt)) {
            throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt", 400);
        }

        if (($exhibitionStartsAt <= $dailyRoomExhibitionStartsAt) && ($exhibitionEndsAt >= $dailyRoomExhibitionEndsAt)) {
            throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt", 400);
        }
    }
}
