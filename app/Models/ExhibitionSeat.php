<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionSeat extends Model
{
    use HasFactory;

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

    public function seat_status()
    {
        return $this->belongsTo(SeatStatus::class, 'seat_status_id', 'uuid');

    }
}
