<?php

namespace App\UseCases;

use App\Domain\Repositories\FilmRepositoryInterface;

class ListFilmsUseCase
{
    public function __construct(private readonly FilmRepositoryInterface $filmRepository)
    { }

    public function execute(array $filters)
    {
        return $this->filmRepository->getWithFilters($filters);
    }
}
