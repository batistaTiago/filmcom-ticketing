<?php

namespace App\Models;

use App\Domain\DTO\Cart\CartStatusDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class CartStatus extends Model
{
    use BaseModel;
    public $primaryKey = 'uuid';
    public $incrementing = false;

    public const ACTIVE = 'active';
    public const AWAITING_PAYMENT = 'awaiting_payment';
    public const FINISHED = 'finished';
    public const EXPIRED = 'expired';
    public const CANCELED = 'canceled';

    public function toDto(): CartStatusDTO
    {
        return CartStatusDTO::fromArray($this->attributes);
    }

}
