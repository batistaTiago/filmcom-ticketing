<?php

namespace App\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Support\Str;

class MysqlCartRepository implements CartRepositoryInterface
{
    public function getCart(string $uuid): CartDTO
    {
        return Cart::query()->firstWhere(compact('uuid'))->toDto();
    }

    public function getOrCreateCart(string $userUuid, ?string $cartUuid = null): CartDTO
    {
        $baseCartData = [
            'user_id' => $userUuid,
            'cart_status_id' => CartStatus::query()
                ->where(['name' => CartStatus::ACTIVE])
                ->firstOrFail()
                ->uuid,
        ];

        return (empty($cartUuid) ?
            Cart::query()->create($this->getCreateCartData($baseCartData)) :
            Cart::query()->firstWhere($baseCartData) ??
            Cart::query()->create($this->getCreateCartData($baseCartData)))->toDto();
    }

    private function getCreateCartData(array $baseCartData): array
    {
        return array_merge($baseCartData, ['uuid' => Str::orderedUuid()->toString()]);
    }
}
