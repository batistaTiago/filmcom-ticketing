<?php

namespace App\Models;

use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatStatus extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $hidden = ['pivot'];

    public const AVAILABLE = 'available';
    public const RESERVED = 'reserved';
    public const SOLD = 'sold';
    public const UNAVAILABLE = 'unavailable';

    public const DEFAULT = self::AVAILABLE;

    public function exhibition_seats()
    {
        return $this->hasMany(ExhibitionSeat::class, 'seat_status_id', 'uuid');
    }
}
