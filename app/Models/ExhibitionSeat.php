<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionSeat extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;
    public $fillable = [
        'uuid',
        'exhibition_id',
        'theater_room_seat_id',
        'seat_status_id',
    ];
}
