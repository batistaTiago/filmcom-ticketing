<?php

namespace App\Jobs;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
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
        CartStatusRepositoryInterface $cartStatusRepository,
        CartRepositoryInterface $cartRepository
    ): void
    {
        // TODO process payment and make tickets available to the buyer
        $cartRepository->updateStatus($this->cart_id, $cartStatusRepository->getByName(CartStatus::FINISHED));
    }

    public function tags(): array
    {
        return [
            "process-cart-checkout",
            "process-cart-checkout:" . $this->cart_id,
        ];
    }
}
