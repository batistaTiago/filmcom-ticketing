<?php

namespace App\Http\Controllers;

use App\UseCases\ListTicketTypesUseCase;
use Illuminate\Http\Request;

class TicketTypeController
{
    public function index(Request $request, ListTicketTypesUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }
}
