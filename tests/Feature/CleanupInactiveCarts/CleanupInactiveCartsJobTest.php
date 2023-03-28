<?php

namespace CleanupInactiveCarts;

namespace Tests\Feature\CleanupInactiveCarts;

use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Jobs\CleanupInactiveCartJob;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\Ticket;
use Tests\TestCase;

class CleanupInactiveCartsJobTest extends TestCase
{
    public function testHandle(): void
    {
        $activeStatus = CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        $expiredStatus = CartStatus::factory()->create(['name' => CartStatus::EXPIRED]);

        $cart = Cart::factory()->create(['cart_status_id' => $expiredStatus->uuid]);
        $tickets = Ticket::factory()->count(3)->create(['cart_id' => $cart->uuid]);

        $otherCart = Cart::factory()->create(['cart_status_id' => $activeStatus->uuid]);
        $otherTickets = Ticket::factory()->count(3)->create(['cart_id' => $otherCart->uuid]);

        $cartStatusRepository = $this->app->make(CartStatusRepositoryInterface::class);
        $job = new CleanupInactiveCartJob($cart->uuid);
        $job->handle($cartStatusRepository);

        $this->assertDatabaseMissing('tickets', ['cart_id' => $cart->uuid]);

        foreach ($otherTickets as $otherTicket) {
            $this->assertDatabaseHas('tickets', ['uuid' => $otherTicket->uuid, 'cart_id' => $otherCart->uuid]);
        }

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart->uuid,
            'cart_status_id' => $expiredStatus->uuid,
        ]);
    }
}
