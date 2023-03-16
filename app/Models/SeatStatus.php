<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatStatus extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public const REGULAR = 'regular';
    public const LARGE = 'large';
    public const WHEEL_CHAIR = 'wheel_chair';
}
