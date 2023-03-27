<?php

namespace App\Domain\DTO;

use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use InvalidArgumentException;

class TicketDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $cart_id,
        public ?TheaterRoomSeatDTO $seat = null,
        public ?ExhibitionDTO $exhibition = null,
        public ?TicketTypeDTO $type = null,
        public ?ExhibitionTicketTypeDTO $ticketTypeExhibitionInfo = null,
    ) {
        if (empty($this->uuid)) {
            throw new InvalidArgumentException('Uuid must not be empty');
        }

        if (empty($this->cart_id)) {
            throw new InvalidArgumentException('Cart ID must not be empty');
        }
    }
}
