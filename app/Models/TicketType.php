<?php

namespace App\Models;

use App\Domain\DTO\TicketTypeDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public const REGULAR = 'regular';
    public const STUDENT = 'student';
    public const COURTESY = 'courtesy';

    public function toDto(): TicketTypeDTO
    {
        return TicketTypeDTO::fromArray($this->toArray());
    }
}
