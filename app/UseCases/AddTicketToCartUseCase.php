<?php

namespace App\UseCases;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\TicketRepositoryInterface;
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Services\ComputeCartStateService;
use App\Services\TicketAvailabilityService;
use Illuminate\Auth\AuthManager;

class AddTicketToCartUseCase
{
    public function __construct(
        private readonly TicketAvailabilityService $ticketAvailabilityService,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly AuthManager $auth,
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly ComputeCartStateService $cartStateService,
    ) { }

    public function execute(array $data)
    {
        $this->ticketAvailabilityService->execute(
            $data['exhibition_id'],
            $data['ticket_type_id'],
            $data['theater_room_seat_id'],
        );

        $cart = $this->cartRepository->getOrCreateCart($this->auth->user()->uuid, $data['cart_id'] ?? null);
        $this->ticketRepository->createTicketInCart(
            $cart->uuid,
            $data['exhibition_id'],
            $data['theater_room_seat_id'],
            $data['ticket_type_id'],
        );

        // TODO layerize this code
        ExhibitionSeat::query()->where([
            'exhibition_id' => $data['exhibition_id'],
            'theater_room_seat_id' => $data['theater_room_seat_id'],
        ])->update([
            'seat_status_id' => SeatStatus::query()->firstWhere(['name' => SeatStatus::RESERVED])->uuid
        ]);


        return response()->json(['cart_state' => $this->cartStateService->execute($cart->uuid)]);
    }
}
