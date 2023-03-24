<?php

namespace App\Models;

use App\Domain\DTO\ExhibitionSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatStatusDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ExhibitionSeat extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $hidden = ['pivot'];

    public $fillable = [
        'uuid',
        'exhibition_id',
        'theater_room_seat_id',
        'seat_status_id',
    ];

    public function seat()
    {
        return $this->belongsTo(TheaterRoomSeat::class, 'theater_room_seat_id', 'uuid');
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class, 'exhibition_id', 'uuid');
    }

    public function seat_status()
    {
        return $this->belongsTo(SeatStatus::class, 'seat_status_id', 'uuid');
    }

    public function toDto(): ExhibitionSeatDTO
    {
        $data = $this->toArray();

        return new ExhibitionSeatDTO(
            $this->uuid,
            $this->exhibition_id,
            $this->theater_room_seat_id,
            !empty($data['seat_status']) ? TheaterRoomSeatStatusDTO::fromArray($data['seat_status']) : null
        );
    }
}
