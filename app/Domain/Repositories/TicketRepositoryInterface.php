<?php

namespace App\Domain\Repositories;

use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    public function findTicketsInCart(string $cartUuid): Collection;
    public function removeTicketFromCart(string $cart_id, string $ticket_id);
}
