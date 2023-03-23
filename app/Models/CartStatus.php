<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartStatus extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public const ACTIVE = 'active';
    public const AWAITING_PAYMENT = 'awaiting_payment';
    public const FINISHED = 'finished';
    public const EXPIRED = 'expired';
    public const CANCELED = 'canceled';
}
