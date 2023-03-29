<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Http\Requests\AddTicketToCartRequest;
use App\Http\Requests\GoToCheckoutRequest;
use App\Http\Requests\RemoveTicketFromCartRequest;
use App\Jobs\Checkout\IssueTicketsJob;
use App\Jobs\Checkout\ProcessCartPaymentJob;
use App\Jobs\Checkout\SendPurchaseCompleteEmailJob;
use App\Models\CartStatus;
use App\Services\ComputeCartStateService;
use App\UseCases\AddTicketToCartUseCase;
use App\UseCases\RemoveTicketFromCartUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

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
        // TODO layerize this code
        $cart = $cartRepository->getActiveUserCart($request->cart_id, $request->user()->uuid);
        $awaitPaymentStatus = $repo->getByName(CartStatus::AWAITING_PAYMENT);
        $cartRepository->updateStatus($cart, $awaitPaymentStatus);

        Bus::chain([
            new ProcessCartPaymentJob($cart->uuid),
            new IssueTicketsJob($cart->uuid),
            new SendPurchaseCompleteEmailJob($cart->uuid),
        ])->dispatch();

        return response()->json(['cart_state' => $cartStateService->execute($cart->uuid)]);
    }

    public function myPurchases(
        Request $request,
        CartRepositoryInterface $cartRepository,
    )
    {
        // TODO layerize this code
        $carts = $cartRepository->getFinishedUserCarts($request->user()->uuid);
        return response()->json($carts);
    }
}
