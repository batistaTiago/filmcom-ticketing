<?php

namespace App\Models;

use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use BaseModel;

    public $fillable = [
        'uuid',
        'user_id',
        'cart_status_id',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'cart_id', 'uuid');
    }
}
