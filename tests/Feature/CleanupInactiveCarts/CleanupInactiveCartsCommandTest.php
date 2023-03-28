<?php

namespace Tests\Feature\CleanupInactiveCarts;

use App\Console\Commands\Schedule\CleanupInactiveCartsCommand;
use App\Jobs\CleanupInactiveCartJob;
use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CleanupInactiveCartsCommandTest extends TestCase
{
    /**
     * @test
     * @dataProvider cartsDataProvider
     */
    public function should_dispatch_jobs_for_every_active_cart_that_hasnt_been_updated_in_a_certain_time(
        int $oldCartMinutes,
        int $recentCartMinutes,
        bool $shouldDispatchOldCart,
        bool $shouldDispatchRecentCart
    ): void
    {
        Carbon::setTestNow(Carbon::create('2023', '03', '27', '23', '11'));
        $activeStatus = CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        CartStatus::query()->where('name', CartStatus::EXPIRED)->first();
        $oldCart = Cart::factory()->create([
            'cart_status_id' => $activeStatus->uuid,
            'updated_at' => Carbon::now()->subMinutes($oldCartMinutes),
        ]);
        $recentCart = Cart::factory()->create([
            'cart_status_id' => $activeStatus->uuid,
            'updated_at' => Carbon::now()->subMinutes($recentCartMinutes),
        ]);

        Bus::fake();
        $this->artisan('carts:cleanup');

        if ($shouldDispatchOldCart) {
            Bus::assertDispatched(CleanupInactiveCartJob::class, function ($job) use ($oldCart) {
                return $job->cart_id === $oldCart->uuid;
            });
        } else {
            Bus::assertNotDispatched(CleanupInactiveCartJob::class, function ($job) use ($oldCart) {
                return $job->cart_id === $oldCart->uuid;
            });
        }

        if ($shouldDispatchRecentCart) {
            Bus::assertDispatched(CleanupInactiveCartJob::class, function ($job) use ($recentCart) {
                return $job->cart_id === $recentCart->uuid;
            });
        } else {
            Bus::assertNotDispatched(CleanupInactiveCartJob::class, function ($job) use ($recentCart) {
                return $job->cart_id === $recentCart->uuid;
            });
        }
    }

    public static function cartsDataProvider(): array
    {
        $tolerance = CleanupInactiveCartsCommand::TOLERANCE_MINUTES;

        return [
            [$tolerance + 1, $tolerance - 1, true, false],
            [$tolerance - 1, $tolerance + 1, false, true],
            [$tolerance, $tolerance, false, false],
            [$tolerance - 5, $tolerance - 5, false, false],
            [$tolerance + 5, $tolerance + 5, true, true],
            [$tolerance + 10, $tolerance - 10, true, false],
        ];
    }
}
