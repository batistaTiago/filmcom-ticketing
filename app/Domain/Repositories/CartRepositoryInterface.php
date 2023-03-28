<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\UserDTO;

interface CartRepositoryInterface
{
    public function getCart(string $uuid): CartDTO;
    public function getActiveUserCart(string $uuid, UserDTO|string $userInput): CartDTO;
    public function getOrCreateCart(string $userUuid, ?string $cartUuid = null): CartDTO;
    public function updateStatus(string|CartDTO $inputCart, string|CartStatusDTO $inputStatus): void;
}
