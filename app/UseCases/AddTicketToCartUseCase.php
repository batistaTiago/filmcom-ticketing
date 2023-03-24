<?php

namespace App\UseCases;

use App\Domain\Services\ComputeCartStateService;
use App\Models\Cart;
use App\Models\CartStatus;
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Models\Ticket;
use App\Services\TicketAvailabilityService;
use DomainException;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Str;

class AddTicketToCartUseCase
{
    public function __construct(
        private readonly TicketAvailabilityService $ticketAvailabilityService,
        private readonly AuthManager $auth,
        private readonly ComputeCartStateService $computeCartStateService
    ) { }

    public function execute(array $data)
    {
        $this->ticketAvailabilityService->execute(
            $data['exhibition_id'],
            $data['ticket_type_id'],
            $data['theater_room_seat_id'],
        );

        // TODO layerize this code
        $cart = $this->getOrCreateCart($this->auth->user()->uuid, $data['cart_id'] ?? null);
        $ticket = $this->createTicketInCart(
            $data['exhibition_id'],
            $data['theater_room_seat_id'],
            $data['ticket_type_id'],
            $cart->uuid
        );

        ExhibitionSeat::query()->where([
            'exhibition_id' => $data['exhibition_id'],
            'theater_room_seat_id' => $data['theater_room_seat_id'],
        ])->update([
            'seat_status_id' => SeatStatus::query()->firstWhere(['name' => SeatStatus::RESERVED])->uuid
        ]);

        return response()->json([
            'ticket' => $ticket->uuid
        ]);
    }

    private function getOrCreateCart(string $userUuid, ?string $cartUuid = null): Cart
    {
        $baseCartData = [
            'user_id' => $userUuid,
            'cart_status_id' => CartStatus::query()
                ->where(['name' => CartStatus::ACTIVE])
                ->firstOrFail()
                ->uuid,
        ];

        return empty($cartUuid) ?
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
