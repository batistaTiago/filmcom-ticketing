<?php

namespace App\UseCases;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;
use App\Domain\Repositories\TheaterRoomRepositoryInterface;

class CreateTheaterRoomUseCase
{
    public function __construct(private readonly TheaterRoomRepositoryInterface $theaterRoomRepository)
    { }

    public function execute(array $data): TheaterRoomDTO
    {
        $dto = TheaterRoomDTO::fromArray($data);
        $this->theaterRoomRepository->create($dto);
        return $dto;
    }
}
