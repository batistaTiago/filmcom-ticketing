<?php

namespace App\UseCases;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\Repositories\TicketRepositoryInterface;
use App\Services\ComputeCartStateService;

class RemoveTicketFromCartUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly ComputeCartStateService $cartStateService,
    ) { }

    public function execute(array $data): CartDTO
    {
        $this->ticketRepository->removeTicketFromCart($data['cart_id'], $data['ticket_id']);
        return $this->cartStateService->execute($data['cart_id']);
    }
}
