<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTicketToCartRequest;
use App\UseCases\AddTicketToCartUseCase;

class TicketController
{
    public function addToCart(AddTicketToCartRequest $request, AddTicketToCartUseCase $useCase)
    {
        return $useCase->execute($request->validated());
    }
}
