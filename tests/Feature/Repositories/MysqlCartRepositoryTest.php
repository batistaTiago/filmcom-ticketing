<?php

namespace Tests\Feature\Repositories;

use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
use Tests\TestCase;

class MysqlCartRepositoryTest extends TestCase
{
    /**
     * @dataProvider updateStatusDataProvider
     */
    public function testUpdateStatus(
        $inputCart,
        $inputStatus
    ): void {
        $cartStatusRepository = $this->app->make(CartStatusRepositoryInterface::class);
        $sut = $this->app->make(CartRepositoryInterface::class);

        $activeStatus = CartStatus::factory()->create(['name' => 'active']);
        $expiredStatus = CartStatus::factory()->create(['name' => 'expired']);

        $cart1 = Cart::factory()->create(['cart_status_id' => $activeStatus->uuid]);
        $cart2 = Cart::factory()->create(['cart_status_id' => $activeStatus->uuid]);

        $inputCart = $inputCart === 'cart1_uuid' ? $cart1->uuid : $sut->getCart($cart1->uuid);
        $inputStatus = $inputStatus === 'expired_uuid' ? $expiredStatus->uuid : $cartStatusRepository->getByName('expired');

        $sut->updateStatus($inputCart, $inputStatus);

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart1->uuid,
            'cart_status_id' => $expiredStatus->uuid,
        ]);

        $this->assertDatabaseHas('carts', [
            'uuid' => $cart2->uuid,
            'cart_status_id' => $activeStatus->uuid,
        ]);
    }

    public static function updateStatusDataProvider(): array
    {
        return [
            ['cart1_uuid', 'expired_uuid'],
            ['cart1_dto', 'expired_uuid'],
            ['cart1_uuid', 'expired_status_dto'],
        ];
    }
}
