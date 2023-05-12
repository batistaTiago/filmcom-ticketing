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
    public const ITEMS_PER_PAGE = 5;
    protected $signature = 'carts:cleanup';
    protected $description = 'Command description';

    public function handle()
    {
        $this->info("Fetching inactive carts...");
        Cart::query()
            ->select('uuid')
            ->where(Cart::UPDATED_AT, '<', now()->subMinutes(self::TOLERANCE_MINUTES))
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::ACTIVE))
            ->chunk(self::ITEMS_PER_PAGE, function (Collection $carts) {
                foreach ($carts as $cart) {
                    $this->info("Dispatching job for $cart->uuid");
                    CleanupInactiveCartJob::dispatch($cart->uuid);
                }
            });
        $this->info("Done");
    }
}
