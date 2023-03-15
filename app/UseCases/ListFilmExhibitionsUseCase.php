<?php

namespace App\UseCases;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use Illuminate\Support\Collection;

class ListFilmExhibitionsUseCase
{
    public function __construct(private readonly ExhibitionRepositoryInterface $exhibitionRepository)
    { }

    public function execute(string $film_id): Collection
    {
        return $this->exhibitionRepository->getFilmExhibitions($film_id);
    }
}
