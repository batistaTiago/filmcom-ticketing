<?php

namespace App\Services;

use App\Domain\Repositories\ExhibitionSeatRepositoryInterface;
use App\Domain\Repositories\ExhibitionTicketTypeRepositoryInterface;
use App\Models\ExhibitionSeat;
use App\Models\ExhibitionTicketType;
use App\Models\SeatStatus;
use DomainException;

class TicketAvailabilityService
{
    public function __construct(
        private readonly ExhibitionTicketTypeRepositoryInterface $exhibitionTicketTypeRepository,
        private readonly ExhibitionSeatRepositoryInterface $exhibitionSeatRepository
    )
    {

    }
    public function execute($exhibition_id, $ticket_type_id, $theater_room_seat_id)
    {
        if (!$this->exhibitionTicketTypeRepository->exists($exhibition_id, $ticket_type_id)) {
            throw new DomainException('This ticket type is not available for the chosen exhibition');
        }

        $exhibitionSeat = $this->exhibitionSeatRepository->findExhibitionSeat($exhibition_id, $theater_room_seat_id);

        if ($exhibitionSeat->seat_status->name !== SeatStatus::AVAILABLE) {
            throw new DomainException('This seat is not available for the chosen exhibition');
        }
    }
}
