<?php

namespace App\UseCases;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;
use App\Domain\Repositories\TheaterRoomRepositoryInterface;

class ShowTheaterRoomAvailabilityUseCase
{
    public function __construct(private readonly TheaterRoomRepositoryInterface $theaterRoomRepository)
    {
    }

    public function execute(array $data): TheaterRoomDTO
    {
        return $this->theaterRoomRepository->findRoomAvailability($data['room_id'], $data['exhibition_id']);
    }
}
