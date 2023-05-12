<?php

namespace App\Jobs\Checkout;

use App\Mail\PurchaseCompleteMailable;
use App\Models\User;
use App\Services\ComputeCartStateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPurchaseCompleteEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly string $cart_id)
    { }

    public function handle(Mailer $mailer, ComputeCartStateService $cartStateService): void
    {
        $user = User::query()->whereHas('carts', fn ($query) => $query->where('uuid', $this->cart_id))->first();
        $mailable = new PurchaseCompleteMailable($cartStateService->execute($this->cart_id));

        $mailer->to($user->email)->send($mailable);
    }

    public function tags(): array
    {
        return [
            "send-purchase-complete-email",
            "send-purchase-complete-email:" . $this->cart_id,
        ];
    }
}
