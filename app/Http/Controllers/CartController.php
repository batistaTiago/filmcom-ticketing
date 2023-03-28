<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\CartRepositoryInterface;
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
        CartRepositoryInterface $cartRepository,
    )
    {
        $cart = $cartRepository->getActiveUserCart($request->cart_id, $request->user()->toDto());

        $awaitPaymentStatus = $repo->getByName(CartStatus::AWAITING_PAYMENT);
        $cartRepository->updateStatus($cart, $awaitPaymentStatus);

        ProcessCartCheckoutJob::dispatch($cart->uuid);

        return response()->json(['cart_state' => $cartStateService->execute($cart->uuid)]);
    }
}
