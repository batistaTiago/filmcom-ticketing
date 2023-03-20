<?php

namespace App\Services;

use App\Domain\DTO\UserDTO;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

class AuthTokenGenerator
{
    public function createToken(UserDTO $user, string $name, array $abilities = ['*'])
    {
        $token = PersonalAccessToken::query()->create([
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
            'expires_at' => now()->addMinutes(config('auth.passwords.users.expire')),
            'tokenable_type' => User::class,
            'tokenable_id' => $user->uuid,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }
}
