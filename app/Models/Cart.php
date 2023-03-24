<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

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
