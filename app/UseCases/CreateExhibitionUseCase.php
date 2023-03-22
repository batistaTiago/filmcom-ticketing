<?php

namespace App\UseCases;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Jobs\CreateExhibitionSeatAvailabilityJob;
use App\Jobs\CreateExhibitionTicketTypeAvailabilityJob;

class CreateExhibitionUseCase
{
    public function __construct(
        private readonly ExhibitionRepositoryInterface $exhibitionRepository,
        private readonly RoomAvailabilityServiceInterface $roomAvailabilityService,
    )
    { }

    public function execute(array $exhibitionData, array $ticketTypeUuids): ExhibitionDTO
    {
        $newExhibition = ExhibitionDTO::fromArray($exhibitionData);
        $this->roomAvailabilityService->validate($newExhibition);
        $this->exhibitionRepository->create($newExhibition);

        // TODO move to a job dispatcher interface
        CreateExhibitionSeatAvailabilityJob::dispatch($newExhibition);
        CreateExhibitionTicketTypeAvailabilityJob::dispatch($newExhibition, $ticketTypeUuids);

        return $newExhibition;
    }
}
