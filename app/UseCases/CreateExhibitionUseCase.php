<?php

namespace App\UseCases;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Repositories\FilmRepositoryInterface;
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
        $day_of_week = $data['day_of_week'];
        $theater_room_id = $data['theater_room_id'];
        $film_id = $data['film_id'];

        $exhibitionFilm = $this->filmRepository->findByUuid($film_id);

        $exhibitionStartsAt = Carbon::parse($data['starts_at']);
        $exhibitionEndsAt = $exhibitionStartsAt->clone()->addMinutes($exhibitionFilm->duration);

        // TODO considerar apenas is_active true na query de exibicoes diarias abaixo
        $dailyRoomExhibitions = $this->exhibitionRepository->getDailyExhibitionsInRoom($day_of_week, $theater_room_id);
        $dailyRoomFilms = $this->filmRepository->getByUuids($dailyRoomExhibitions->pluck('film_id'))->groupBy('uuid');

        foreach ($dailyRoomExhibitions as $dailyRoomExhibition) {
            $film = $dailyRoomFilms[$dailyRoomExhibition->film_id]->first();

            $dailyRoomExhibitionStartsAt = Carbon::parse($dailyRoomExhibition->starts_at);
            $dailyRoomExhibitionEndsAt = $dailyRoomExhibitionStartsAt->clone()->addMinutes($film->duration);

            if (($dailyRoomExhibitionStartsAt < $exhibitionStartsAt) && ($dailyRoomExhibitionEndsAt > $exhibitionStartsAt)) {
                throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt", 400);
            }

            if (($dailyRoomExhibitionStartsAt < $exhibitionEndsAt) && ($dailyRoomExhibitionEndsAt > $exhibitionEndsAt)) {
                throw new DomainException("A session is already supposed to take place at this time: $dailyRoomExhibitionStartsAt to $dailyRoomExhibitionEndsAt", 400);
            }
        }

        $exhibition = ExhibitionDTO::fromArray($data);

        $this->exhibitionRepository->create($exhibition);
        return $exhibition;
    }
}
