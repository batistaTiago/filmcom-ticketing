<?php

namespace App\Domain\Repositories;

use Illuminate\Support\Collection;

interface TicketTypeRepositoryInterface
{
    public function getTicketTypes(): Collection;
    public function getExhibitionTicketTypes(string $exhibition_id): Collection;
}
