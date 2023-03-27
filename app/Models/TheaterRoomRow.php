<?php

namespace App\Models;

use App\Domain\DTO\TheaterRoom\TheaterRoomRowDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TheaterRoomRow extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'name',
        'theater_room_id'
    ];

    public function seats()
    {
        return $this->hasMany(TheaterRoomSeat::class, 'theater_room_row_id', 'uuid');
    }

    public function room()
    {
        return $this->belongsTo(TheaterRoom::class, 'theater_room_id', 'uuid');
    }

    public function toDTO(): TheaterRoomRowDTO
    {
        $data = $this->toArray();

        return new TheaterRoomRowDTO(
            uuid: $data['uuid'],
            name: $data['name'],
            seats: !empty($data['seats']) ? $this->convertSeatsToDTO($this->seats) : []
        );
    }

    private function convertSeatsToDTO(Collection $seats)
    {
        return $seats->map(function (TheaterRoomSeat $seat) {
            return $seat->toDTO();
        })->toArray();
    }
}
