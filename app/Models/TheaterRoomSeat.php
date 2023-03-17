<?php

namespace App\Models;

use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatTypeDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheaterRoomSeat extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;
    public $hidden = ['pivot'];

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

    public function exhibition_seats()
    {
        return $this->hasMany(ExhibitionSeat::class, 'theater_room_seat_id', 'uuid');
    }

    public function availabilityStatus()
    {
//        return $this->through('exhibition_seats')->has('seat_status');
        return $this->belongsToMany(
            SeatStatus::class,
            ExhibitionSeat::class,
            'theater_room_seat_id',
            'seat_status_id',
            'uuid',
            'uuid',
        );
    }

    public function toDTO(): TheaterRoomSeatDTO
    {
        $data = $this->toArray();

        $status = isset($data['exhibition_seats'][0]['seat_status']['name']) ?
            new TheaterRoomSeatStatusDTO($data['exhibition_seats'][0]['seat_status']['name']) :
            null;

        return new TheaterRoomSeatDTO(
            uuid: $data['uuid'],
            name: $data['name'],
            type: new TheaterRoomSeatTypeDTO($data['type']['name'] ?? SeatType::REGULAR),
            status: $status,
        );
    }
}
