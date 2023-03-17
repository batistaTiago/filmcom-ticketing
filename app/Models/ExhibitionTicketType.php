<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionTicketType extends Model
{
    use HasFactory;
    public $primaryKey = 'uuid';
    public $incrementing = false;

    public function ticket_type()
    {
        return $this->hasOne(TicketType::class, 'uuid', 'ticket_type_id');
    }
}
