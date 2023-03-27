<?php

namespace App\Domain\Repositories;

use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    public function findTicketsInCart(string $cartUuid): Collection;
}
