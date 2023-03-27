<?php

namespace App\Services;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ComputeCartStateService
{
    private ?CartDTO $cart = null;
    private ?Collection $tickets = null;

    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly TicketRepositoryInterface $ticketRepository,
    ) { }

    public function execute(string $cart_uuid)
    {
        $cart = $this->cart ?? $this->cartRepository->getCart($cart_uuid);
        $cart->tickets = $this->tickets ?? $this->ticketRepository->findTicketsInCart($cart->uuid);
        return $cart;
    }

    public function setCart(CartDTO $cartDTO)
    {
        if (!is_null($this->tickets)) {
            foreach ($this->tickets as $ticket) {
                if ($ticket->cart_id != $cartDTO) {
                    throw new InvalidArgumentException('Tickets must belong to the preloaded cart');
                }
            }
        }
        $this->cart = $cartDTO;
    }

    public function setTickets(Collection $tickets)
    {
        foreach ($tickets as $ticket) {
            if (!($ticket instanceof TicketDTO)) {
                throw new InvalidArgumentException('Tickets must be a Collection of TicketDTO objects');
            }
        }

        $this->tickets = $tickets;
    }
}
