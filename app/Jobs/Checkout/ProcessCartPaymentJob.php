<?php

namespace App\Jobs\Checkout;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCartPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly string $cart_id)
    { }

    public function handle(): void
    {
        Log::info('Attempting simulated payment service');
        sleep(1);
        Log::info('Simulated payment service ok');
        return;
    }

    public function tags(): array
    {
        return [
            "process-cart-payment",
            "process-cart-payment:" . $this->cart_id,
        ];
    }
}
