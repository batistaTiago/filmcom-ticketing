<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\UserDTO;
use Illuminate\Support\Collection;

interface CartRepositoryInterface
{
    // TODO refactor using interface segregation
    public function exists(string|CartDTO $input): bool;
    public function getCart(string $uuid): CartDTO;
    public function getActiveUserCart(string $uuid, UserDTO|string $userInput): CartDTO;
    public function getFinishedUserCarts(UserDTO|string $userInput): Collection;
    public function getOrCreateActiveCart(string $userUuid, ?string $cartUuid = null): CartDTO;
    public function updateStatus(string|CartDTO $inputCart, string|CartStatusDTO $inputStatus): void;

    public function issueTickets(string|CartDTO $inputCart): void;
}
