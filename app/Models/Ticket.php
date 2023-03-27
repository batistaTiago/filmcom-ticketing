<?php

namespace App\Models;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\ExhibitionTicketTypeDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatTypeDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\TicketTypeDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'exhibition_id',
        'ticket_type_id',
        'theater_room_seat_id',
        'cart_id'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class, 'exhibition_id', 'uuid');
    }

    public function type()
    {
        return $this->hasOne(TicketType::class, 'uuid', 'ticket_type_id');
    }

    public function seat()
    {
        return $this->belongsTo(TheaterRoomSeat::class, 'theater_room_seat_id', 'uuid');
    }

    public function exhibition_ticket_types()
    {
        return $this->hasMany(
            ExhibitionTicketType::class,
            'ticket_type_id',
            'ticket_type_id'
        );
    }

    public function toDto(): TicketDTO
    {
        $data = $this->toArray();

        return new TicketDTO(
            $data['uuid'],
            $data['cart_id'],
            new TheaterRoomSeatDTO(
                uuid: $data['seat']['uuid'],
                name: $data['seat']['name'],
                type: TheaterRoomSeatTypeDTO::fromArray($data['seat']['type']),
                status: TheaterRoomSeatStatusDTO::fromArray($data['seat']['exhibition_seat']['seat_status']),
            ),
            ExhibitionDTO::fromArray($data['exhibition']),
            TicketTypeDTO::fromArray($data['type']),
            ExhibitionTicketTypeDTO::fromArray($data['exhibition_ticket_type']),
        );
    }
}
