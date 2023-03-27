<?php

namespace App\Services;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\TicketRepositoryInterface;

class ComputeCartStateService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly TicketRepositoryInterface $ticketRepository,
    ) { }

    public function execute(string $cart_uuid)
    {
        $cart = $this->cartRepository->getCart($cart_uuid);
        $cart->tickets = $this->ticketRepository->findTicketsInCart($cart->uuid);
        return $cart;
    }
}
