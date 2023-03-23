<?php

namespace App\UseCases;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Jobs\CreateExhibitionSeatAvailabilityJob;
use App\Jobs\PopulateExhibitionTicketPricingJob;
use Illuminate\Bus\Dispatcher;

class CreateExhibitionUseCase
{
    public function __construct(
        private readonly ExhibitionRepositoryInterface $exhibitionRepository,
        private readonly RoomAvailabilityServiceInterface $roomAvailabilityService,
        private readonly Dispatcher $dispatcher,
    )
    { }

    public function execute(array $exhibitionData, array $ticketTypes): ExhibitionDTO
    {
        $newExhibition = ExhibitionDTO::fromArray($exhibitionData);
        $this->roomAvailabilityService->validate($newExhibition);
        $this->exhibitionRepository->create($newExhibition);

        $this->dispatcher->dispatch(new CreateExhibitionSeatAvailabilityJob($newExhibition));
        $this->dispatcher->dispatch(new PopulateExhibitionTicketPricingJob($newExhibition, $ticketTypes));

        return $newExhibition;
    }
}
