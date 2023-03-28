<?php

namespace App\Models;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\UserDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Cart extends Model
{
    use BaseModel;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function status()
    {
        return $this->hasOne(CartStatus::class, 'uuid', 'cart_status_id');
    }

    public function toDto(): CartDTO
    {
        $data = $this->toArray();

        return new CartDTO(
            uuid: $this->uuid,
            user: UserDTO::fromArray($this->user->attributes),
            status: CartStatusDTO::fromArray($this->status->attributes),
            tickets: !empty($data['tickets']) ? $this->convertTicketsToDTO($data['tickets']) : null,
        );
    }

    private function convertTicketsToDTO(Collection $tickets)
    {
        return $tickets->map(function (Ticket $ticket) {
            return $ticket->toDTO();
        })->toArray();
    }
}
