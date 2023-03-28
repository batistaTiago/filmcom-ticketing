<?php

namespace App\Jobs;

use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CleanupInactiveCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly string $cart_id)
    { }

    public function handle(
        CartStatusRepositoryInterface $cartStatusRepository,
        Connection $databaseConnection
    ): void
    {
        try {
            $databaseConnection->beginTransaction();

            $cart = Cart::query()
                ->with('status')
                ->where(['uuid' => $this->cart_id])
                ->first();

            if ($cart->status->name === CartStatus::ACTIVE) {
                $cart->update(['cart_status_id' => $cartStatusRepository->getByName(CartStatus::EXPIRED)->uuid]);
                $cart->tickets()->delete();

                $databaseConnection->commit();
            }
        } catch (Throwable $e) {
            $databaseConnection->rollBack();
            throw $e;
        }
    }

    public function tags(): array
    {
        return [
            "cleanup-inactive-cart-job",
            "cleanup-inactive-cart-job:" . $this->cart_id,
        ];
    }
}
