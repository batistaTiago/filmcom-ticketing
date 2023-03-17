<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatStatus extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public const AVAILABLE = 'available';
    public const RESERVED = 'reserved';
    public const SOLD = 'sold';
    public const UNAVAILABLE = 'unavailable';
}
