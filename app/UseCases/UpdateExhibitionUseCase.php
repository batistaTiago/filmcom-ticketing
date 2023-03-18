<?php

namespace App\UseCases;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Exhibition;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class UpdateExhibitionUseCase
{
    public function __construct(
        private readonly ExhibitionRepositoryInterface $exhibitionRepository,
        private readonly RoomAvailabilityServiceInterface $roomAvailabilityService
    )
    { }

    public function execute(string $exhibition_id, array $data): void
    {
        $exhibition = $this->exhibitionRepository->findById($exhibition_id);

        $updatedDtoData = array_merge(
            Arr::only((array) $exhibition, ExhibitionDTO::ATTRIBUTES),
            $data,
            ['starts_at' => Carbon::parse($data['starts_at'])->toTimeString()]
        );

        $dto = ExhibitionDTO::fromArray($updatedDtoData);

        $this->roomAvailabilityService->validate($dto);
        $this->exhibitionRepository->update($dto);
    }
}
