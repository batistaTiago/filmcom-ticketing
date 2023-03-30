<?php

namespace App\Http\Controllers;


use App\Models\Cart;
use Illuminate\Http\Request;

class CartHistoryController
{
    public function getCartHistory(Request $request)
    {
        $user = $request->user();
        return response()->json(Cart::query()
            ->whereHas('user', fn ($query) => $query->where('uuid', $user->uuid))
            ->with('history.status')
            ->where('uuid', $request->cart_id)
            ->first());
    }
}
