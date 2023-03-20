<?php

namespace App\Repositories;

use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\User;

class MysqlUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): UserDTO
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            throw new ResourceNotFoundException('User not found');
        }

        return $user->toDto();
    }
}
