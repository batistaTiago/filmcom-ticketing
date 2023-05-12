<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartHistory extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $table = 'cart_history';
    public $fillable = [
        'uuid',
        'cart_id',
        'cart_status_id',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'uuid');
    }

    public function status()
    {
        return $this->hasOne(CartStatus::class, 'uuid', 'cart_status_id');
    }
}
