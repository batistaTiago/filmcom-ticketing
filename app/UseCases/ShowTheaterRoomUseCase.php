<?php

namespace App\UseCases;

use App\Domain\Repositories\TheaterRoomRepositoryInterface;

class ShowTheaterRoomUseCase
{
    public function __construct(private readonly TheaterRoomRepositoryInterface $roomRepository)
    { }

    public function execute(string $roomId)
    {
        return $this->roomRepository->findRoomById($roomId);
    }
}
