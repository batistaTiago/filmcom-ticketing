<?php

namespace App\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;

class MysqlCartRepository implements CartRepositoryInterface
{
    public function getCart(string $uuid): CartDTO
    {
        return Cart::query()->firstWhere(compact('uuid'))->toDto();
    }
}
