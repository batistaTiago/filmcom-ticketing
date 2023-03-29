<?php

namespace App\Jobs\Checkout;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Models\CartStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueTicketsJob implements ShouldQueue
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
        $cartRepository->updateStatus($this->cart_id, $cartStatusRepository->getByName(CartStatus::FINISHED));
        $cartRepository->issueTickets($this->cart_id);
    }

    public function tags(): array
    {
        return [
            "issue-tickets",
            "issue-tickets:" . $this->cart_id,
        ];
    }
}
