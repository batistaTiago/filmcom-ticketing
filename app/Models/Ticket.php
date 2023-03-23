<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'exhibition_id',
        'ticket_type_id',
        'theater_room_seat_id',
        'cart_id'
    ];
}
