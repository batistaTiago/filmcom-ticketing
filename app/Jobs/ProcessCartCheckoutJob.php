<?php

namespace App\Jobs;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Domain\Repositories\ExhibitionSeatRepositoryInterface;
use App\Domain\Repositories\TicketRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\SeatStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCartCheckoutJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly string $cart_id)
    { }

    public function handle(
        CartRepositoryInterface $cartRepository,
        CartStatusRepositoryInterface $cartStatusRepository,
    ): void
    {
        // TODO process payment
        $cartRepository->updateStatus($this->cart_id, $cartStatusRepository->getByName(CartStatus::FINISHED));
        $cartRepository->issueTickets($this->cart_id);
    }

    public function tags(): array
    {
        return [
            "process-cart-checkout",
            "process-cart-checkout:" . $this->cart_id,
        ];
    }
}
