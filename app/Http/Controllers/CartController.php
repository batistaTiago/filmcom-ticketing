<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\AddTicketToCartRequest;
use App\Http\Requests\GoToCheckoutRequest;
use App\Http\Requests\RemoveTicketFromCartRequest;
use App\Jobs\ProcessCartCheckoutJob;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Services\ComputeCartStateService;
use App\UseCases\AddTicketToCartUseCase;
use App\UseCases\RemoveTicketFromCartUseCase;

class CartController
{
    public function addTicket(AddTicketToCartRequest $request, AddTicketToCartUseCase $useCase)
    {
        return $useCase->execute($request->validated());
    }

    public function removeTicket(RemoveTicketFromCartRequest $request, RemoveTicketFromCartUseCase $useCase)
    {
        return response()->json([
            'cart_state' => $useCase->execute($request->validated())
        ]);
    }

    public function goToCheckout(
        GoToCheckoutRequest $request,
        CartStatusRepositoryInterface $repo,
        ComputeCartStateService $cartStateService,
    )
    {
        $user = $request->user();
        $cart = Cart::query()
            ->where('uuid', $request->cart_id)
            ->whereHas('user', fn ($query) => $query->where('uuid', $user->uuid))
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::ACTIVE))
            ->first();

        if (empty($cart)) {
            throw new ResourceNotFoundException('Cart not found');
        }

        Cart::query()->where('uuid', $cart->uuid)->update([
            'cart_status_id' => $repo->getByName(CartStatus::AWAITING_PAYMENT)->uuid
        ]);

        ProcessCartCheckoutJob::dispatch($cart->uuid);

        return response()->json(['cart_state' => $cartStateService->execute($cart->uuid)]);
    }
}
