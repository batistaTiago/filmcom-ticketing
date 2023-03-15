<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    use HasFactory;

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
}
