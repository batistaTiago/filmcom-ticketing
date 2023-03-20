<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public $primaryKey = 'uuid';
    public $incrementing = false;

    public $fillable = [
        'uuid',
        'name',
        'token',
        'abilities',
        'expires_at',
        'tokenable_type',
        'tokenable_id',
    ];
}
