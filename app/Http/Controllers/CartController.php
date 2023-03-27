<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTicketToCartRequest;
use App\Http\Requests\RemoveTicketFromCartRequest;
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
}
