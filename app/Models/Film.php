<?php

namespace App\Models;

use App\Domain\DTO\FilmDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = FilmDTO::ATTRIBUTES;

    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class, 'film_id', 'uuid');
    }

    public function toDTO(): FilmDTO
    {
        return new FilmDTO(
            $this->uuid,
            $this->name,
            $this->year,
            $this->duration,
        );
    }
}
