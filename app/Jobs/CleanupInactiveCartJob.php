<?php

namespace App\Jobs;

use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupInactiveCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly string $cart_id)
    { }

    public function handle(CartStatusRepositoryInterface $cartStatusRepository): void
    {
        $cart = Cart::query()->where(['uuid' => $this->cart_id])->first();
        $cart->update(['cart_status_id' => $cartStatusRepository->getByName(CartStatus::EXPIRED)->uuid]);
        $cart->tickets()->delete();
    }

    public function tags(): array
    {
        return [
            "cleanup-inactive-cart-job",
            "cleanup-inactive-cart-job:" . $this->cart_id,
        ];
    }
}
