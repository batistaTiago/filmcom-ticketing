<?php

namespace App\Models;

use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatTypeDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheaterRoomSeat extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'name',
        'theater_room_row_id',
        'seat_type_id'
    ];

    public function type()
    {
        return $this->hasOne(SeatType::class, 'uuid', 'seat_type_id');
    }

    public function toDTO(): TheaterRoomSeatDTO
    {
        $data = $this->toArray();

        return new TheaterRoomSeatDTO(
            uuid: $data['uuid'],
            name: $data['name'],
            type: new TheaterRoomSeatTypeDTO($data['type']['name'] ?? 'Available'),
        );
    }
}
