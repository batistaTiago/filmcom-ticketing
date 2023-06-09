<?php

namespace App\Repositories;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Domain\Repositories\ExhibitionSeatRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Cart;
use App\Models\CartHistory;
use App\Models\CartStatus;
use App\Models\SeatStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MysqlCartRepository implements CartRepositoryInterface
{
    public function __construct(
        private readonly ExhibitionSeatRepositoryInterface $exhibitionSeatRepository,
        private readonly CartStatusRepositoryInterface $cartStatusRepo
    ) { }

    public function exists(string|CartDTO $input): bool
    {
        $cart_id = $input instanceof CartDTO ? $input->uuid : $input;

        return Cart::query()->where('uuid', $cart_id)->exists();
    }

    public function getCart(string $uuid): CartDTO
    {
        return Cart::query()->firstWhere(compact('uuid'))->toDto();
    }

    public function getActiveUserCart(string $uuid, UserDTO|string $userInput): CartDTO
    {
        $user_id = $userInput instanceof UserDTO ? $userInput->uuid : $userInput;

        $cart = $this->baseQuery()
            ->whereHas('user', fn ($query) => $query->where('uuid', $user_id))
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::ACTIVE))
            ->where('uuid', $uuid)
            ->first();

        if (empty($cart)) {
            throw new ResourceNotFoundException('No active cart was found for this user');
        }

        return $cart->toDto();
    }

    public function getOrCreateActiveCart(string $userUuid, ?string $cartUuid = null): CartDTO
    {
        $baseData = [
            'user_id' => $userUuid,
            'cart_status_id' => $this->cartStatusRepo->getByName(CartStatus::ACTIVE)->uuid,
        ];

        if (empty($cartUuid)) {
            return $this->createFromBaseData($baseData)->toDto();
        }

        return (Cart::query()->firstWhere($baseData) ?? $this->createFromBaseData($baseData))->toDto();
    }

    private function getCreateCartData(array $baseCartData): array
    {
        return array_merge($baseCartData, ['uuid' => Str::orderedUuid()->toString()]);
    }

    private function createFromBaseData(array $baseData): Cart
    {
        /** @var Cart $cart */
        $cart = Cart::query()->create($this->getCreateCartData($baseData));
        $this->createHistoryRecord($cart->uuid, $baseData['cart_status_id']);
        return $cart;
    }

    private function createHistoryRecord($cart_id, $cart_status_id)
    {
        CartHistory::query()->create([
            'uuid' => Str::orderedUuid()->toString(),
            'cart_id' => $cart_id,
            'cart_status_id' => $cart_status_id,
        ]);
    }

    public function updateStatus(string|CartDTO $inputCart, CartStatusDTO|string $inputStatus): void
    {
        $uuid = $inputCart instanceof CartDTO ? $inputCart->uuid : $inputCart;
        $cart_status_id = $inputStatus instanceof CartStatusDTO ? $inputStatus->uuid : $inputStatus;

        Cart::query()->where(compact('uuid'))->update(compact('cart_status_id'));
        $this->createHistoryRecord($uuid, $cart_status_id);
    }

    public function getFinishedUserCarts(UserDTO|string $userInput): Collection
    {
        $user_id = $userInput instanceof UserDTO ? $userInput->uuid : $userInput;

        return $this->baseQuery()
            ->whereHas('user', fn ($query) => $query->where('uuid', $user_id))
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::FINISHED))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn (Cart $cart) => $cart->toDto());
    }

    private function baseQuery(): Builder
    {
        return Cart::query()
            ->has('tickets')
            ->with([
                'user',
                'status',
                'tickets' => function ($query) {
                    $query->with([
                        'type',
                        'seat.row',
                        'seat.type',
                        'seat.exhibition_seats.seat_status',
                        'exhibition',
                        'exhibition_ticket_types'
                    ]);
                }
            ]);
    }

    public function issueTickets(string|CartDTO $inputCart): void
    {
        $uuid = $inputCart instanceof CartDTO ? $inputCart->uuid : $inputCart;
        $cartModel = Cart::query()
            ->with('tickets')
            ->whereHas('status', fn ($query) => $query->where('name', CartStatus::FINISHED))
            ->has('tickets')
            ->where(compact('uuid'))
            ->first();

        if (empty($cartModel)) {
            throw new ResourceNotFoundException('Cart not found');
        }

        $seat_status_id = SeatStatus::query()->where(['name' => SeatStatus::SOLD])->firstOrFail()->uuid;

        $this->exhibitionSeatRepository->changeSeatStatusBatch(
            $cartModel->tickets->pluck('exhibition_id')->first(),
            $cartModel->tickets->pluck('theater_room_seat_id')->toArray(),
            $seat_status_id
        );
    }
}
