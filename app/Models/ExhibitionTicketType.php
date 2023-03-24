<?php

namespace App\Models;

use App\Domain\DTO\ExhibitionTicketTypeDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ExhibitionTicketType extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'exhibition_id',
        'ticket_type_id',
        'price',
    ];

    public function ticket_type()
    {
        return $this->hasOne(TicketType::class, 'uuid', 'ticket_type_id');
    }

    public function toDto(): ExhibitionTicketTypeDTO
    {
        return new ExhibitionTicketTypeDTO(...$this->toArray());
    }
}
