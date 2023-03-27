<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\Cart\CartDTO;

interface CartRepositoryInterface
{
    public function getCart(string $uuid): CartDTO;
    public function getOrCreateCart(string $userUuid, ?string $cartUuid = null): CartDTO;
}
