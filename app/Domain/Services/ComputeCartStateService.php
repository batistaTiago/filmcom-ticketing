<?php

namespace App\Domain\Services;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\Repositories\TicketRepositoryInterface;
use Illuminate\Auth\AuthManager;

class ComputeCartStateService
{
    public function __construct(private readonly TicketRepositoryInterface $ticketRepository)
    {

    }

    public function execute(CartDTO $cart): CartDTO
    {
        $ticketsInCart = $this->ticketRepository->findTicketsInCart($cart->uuid);
//        return $cartDto;
    }
}
