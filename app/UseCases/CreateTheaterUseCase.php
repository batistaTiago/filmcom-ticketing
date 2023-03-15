<?php

namespace App\UseCases;

use App\Domain\DTO\TheaterDTO;
use App\Domain\Repositories\TheaterRepositoryInterface;

class CreateTheaterUseCase
{
    public function __construct(private readonly TheaterRepositoryInterface $theaterRepository)
    { }

    public function execute(array $data): TheaterDTO
    {
        $dto = TheaterDTO::fromArray($data);
        $this->theaterRepository->create($dto);
        return $dto;
    }
}
