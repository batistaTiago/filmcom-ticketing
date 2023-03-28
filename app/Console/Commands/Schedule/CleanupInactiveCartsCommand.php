<?php

namespace App\Console\Commands\Schedule;

use App\Jobs\CleanupInactiveCartJob;
use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CleanupInactiveCartsCommand extends Command
{
    public const TOLERANCE_MINUTES = 15;
    protected $signature = 'carts:cleanup';
    protected $description = 'Command description';

    public function handle()
    {
        Cart::query()
            ->select('uuid')
            ->where(Cart::UPDATED_AT, '<', now()->subMinutes(self::TOLERANCE_MINUTES))
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::ACTIVE))
            ->chunk(10, function (Collection $carts) {
                foreach ($carts as $cart) {
                    CleanupInactiveCartJob::dispatch($cart->uuid);
                }
            });
    }
}
