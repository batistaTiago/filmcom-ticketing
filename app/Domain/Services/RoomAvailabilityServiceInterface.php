<?php

namespace App\Domain\Services;

use App\Domain\DTO\ExhibitionDTO;

interface RoomAvailabilityServiceInterface
{
    public function validate(ExhibitionDTO $exhibition);
}
