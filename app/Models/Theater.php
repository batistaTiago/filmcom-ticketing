<?php

namespace App\Models;

use App\Domain\DTO\TheaterDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'name'
    ];

    public function rooms()
    {
        return $this->hasMany(TheaterRoom::class, 'uuid', 'theater_id');
    }

    public function toDto(): TheaterDTO
    {
        return new TheaterDTO($this->uuid, $this->name);
    }
}
