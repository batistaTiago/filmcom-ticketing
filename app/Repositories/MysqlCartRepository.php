<?php

namespace App\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Cart;
use App\Models\CartStatus;
use Illuminate\Support\Str;

class MysqlCartRepository implements CartRepositoryInterface
{
    public function getCart(string $uuid): CartDTO
    {
        return Cart::query()->firstWhere(compact('uuid'))->toDto();
    }

    public function getActiveUserCart(string $uuid, UserDTO|string $userInput): CartDTO
    {
        $user_id = $userInput instanceof UserDTO ? $userInput->uuid : $userInput;

        $cart = Cart::query()
            ->where('uuid', $uuid)
            ->whereHas('user', fn ($query) => $query->where('uuid', $user_id))
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::ACTIVE))
            ->first();

        if (empty($cart)) {
            throw new ResourceNotFoundException('No active cart was found for this user');
        }

        return $cart->toDto();
    }

    public function getOrCreateCart(string $userUuid, ?string $cartUuid = null): CartDTO
    {
        $cartStatusRepo = resolve(CartStatusRepositoryInterface::class);

        $baseCartData = [
            'user_id' => $userUuid,
            'cart_status_id' => $cartStatusRepo->getByName(CartStatus::ACTIVE)->uuid,
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

    public function updateStatus(string|CartDTO $inputCart, CartStatusDTO|string $inputStatus): void
    {
        $uuid = $inputCart instanceof CartDTO ? $inputCart->uuid : $inputCart;
        $cart_status_id = $inputStatus instanceof CartStatusDTO ? $inputStatus->uuid : $inputStatus;

        Cart::query()->where(compact('uuid'))->update(compact('cart_status_id'));
    }
}
