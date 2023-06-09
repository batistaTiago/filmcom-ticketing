<?php

namespace App\Models;

use App\Domain\DTO\TheaterRoom\TheaterRoomDTO;
use App\Models\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TheaterRoom extends Model
{
    use BaseModel;

    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'name',
        'theater_id',
    ];

    public function rows()
    {
        return $this->hasMany(TheaterRoomRow::class, 'theater_room_id', 'uuid');
    }

    public function theater()
    {
        return $this->belongsTo(Theater::class, 'uuid', 'theater_id');
    }

    public function toDTO(): TheaterRoomDTO
    {
        $data = $this->toArray();

        return new TheaterRoomDTO(
            uuid: $data['uuid'],
            name: $data['name'],
            theater_id: $data['theater_id'],
            rows: !empty($data['rows']) ? $this->convertRowsToDTO($this->rows) : []
        );
    }

    private function convertRowsToDTO(Collection $rows)
    {
        return $rows->map(function (TheaterRoomRow $row) {
            return $row->toDTO();
        })->toArray();
    }
}
