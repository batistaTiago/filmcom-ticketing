<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTicketToCartRequest;
use App\Models\ExhibitionSeat;
use App\Models\ExhibitionTicketType;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\SeatStatus;
use App\Models\Ticket;
use DomainException;
use Illuminate\Support\Str;

class TicketController
{
    public function addToCart(AddTicketToCartRequest $request)
    {
        // TODO extract these validations to another class for ticket availability service
        $exhibitionTicketType = ExhibitionTicketType::query()->firstWhere([
            'exhibition_id' => $request->exhibition_id,
            'ticket_type_id' => $request->ticket_type_id,
        ]);

        if (empty($exhibitionTicketType)) {
            throw new DomainException('This ticket type is not available for the chosen exhibition');
        }

        $exhibitionSeat = ExhibitionSeat::query()->with('seat_status')->firstWhere([
            'exhibition_id' => $request->exhibition_id,
            'theater_room_seat_id' => $request->theater_room_seat_id,
        ]);

        if ($exhibitionSeat->seat_status->name !== SeatStatus::AVAILABLE) {
            throw new DomainException('This seat is not available for the chosen exhibition');
        }

        // TODO layerize this code
        $cart = $this->getOrCreateCart($request->user()->uuid);
        $ticket = $this->createTicketInCart(
            $request->exhibition_id,
            $request->theater_room_seat_id,
            $request->ticket_type_id,
            $cart->uuid
        );

        $exhibitionSeat->update([
            'seat_status_id' => SeatStatus::query()->firstWhere(['name' => SeatStatus::RESERVED])->uuid
        ]);
    }

    private function getOrCreateCart(string $userUuid): Cart
    {
        $baseCartData = [
            'user_id' => $userUuid,
            'cart_status_id' => CartStatus::query()
                ->where(['name' => CartStatus::ACTIVE])
                ->firstOrFail()
                ->uuid,
        ];

        return empty($request->cart_id) ?
            Cart::query()->create($this->getCreateCartData($baseCartData)) :
            Cart::query()->firstWhere($baseCartData) ??
            Cart::query()->create($this->getCreateCartData($baseCartData));
    }

    private function getCreateCartData(array $baseCartData): array
    {
        return array_merge($baseCartData, ['uuid' => Str::orderedUuid()->toString()]);
    }

    private function createTicketInCart(
        string $exhibition_id,
        string $theater_room_seat_id,
        string $ticket_type_id,
        string $cart_id
    ) {
        $ticket = Ticket::query()->firstWhere(compact('exhibition_id', 'theater_room_seat_id'));

        if ($ticket) {
            throw new DomainException('This seat has already been taken. Please try a different one.');
        }

        $uuid = Str::orderedUuid()->toString();

        return Ticket::query()->create(
            compact(
                'uuid',
                'exhibition_id',
                'theater_room_seat_id',
                'ticket_type_id',
                'cart_id'
            )
        );
    }
}
