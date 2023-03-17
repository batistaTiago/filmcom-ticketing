<?php

namespace App\Models;

use App\Domain\DTO\ExhibitionDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    public $primaryKey = 'uuid';
    public $incrementing = false;
    public $fillable = ExhibitionDTO::ATTRIBUTES;

    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id', 'uuid');
    }

    public function theaterRoom()
    {
        return $this->hasOne(TheaterRoom::class, 'uuid', 'theater_room_id');
    }

    public function toDTO(): ExhibitionDTO
    {
        $data = $this->toArray();

        return new ExhibitionDTO(
            uuid: $data['uuid'],
            film_id: $data['film_id'],
            theater_room_id: $data['theater_room_id'],
            starts_at: new Carbon($data['starts_at']),
            day_of_week: $data['day_of_week'],
            is_active: $data['is_active'],
        );
    }
}
