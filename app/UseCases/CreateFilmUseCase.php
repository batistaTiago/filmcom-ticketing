<?php

namespace App\UseCases;

use App\Domain\DTO\FilmDTO;
use App\Domain\Repositories\FilmRepositoryInterface;

class CreateFilmUseCase
{
    public function __construct(private readonly FilmRepositoryInterface $filmRepository)
    { }

    public function execute(array $data): FilmDTO
    {
        $dto = FilmDTO::fromArray($data);
        $this->filmRepository->create($dto);
        return $dto;
    }
}
