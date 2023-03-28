<?php

namespace Tests\Feature\CleanupInactiveCarts;

use App\Jobs\CleanupInactiveCartJob;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\Ticket;
use Tests\TestCase;

class CleanupInactiveCartJobTest extends TestCase
{
    public function testHandle(): void
    {
        $activeStatus = CartStatus::factory()->create(['name' => CartStatus::ACTIVE]);
        $expiredStatus = CartStatus::factory()->create(['name' => CartStatus::EXPIRED]);
        $awaitingPaymentStatus = CartStatus::factory()->create(['name' => CartStatus::AWAITING_PAYMENT]);

        $cart = Cart::factory()->create(['cart_status_id' => $activeStatus->uuid]);
        Ticket::factory()->count(3)->create(['cart_id' => $cart->uuid]);

        $otherCart = Cart::factory()->create(['cart_status_id' => $awaitingPaymentStatus->uuid]);
        $otherTickets = Ticket::factory()->count(3)->create(['cart_id' => $otherCart->uuid]);

        CleanupInactiveCartJob::dispatch($cart->uuid);

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
