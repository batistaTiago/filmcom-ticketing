<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\UserDTO;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): UserDTO;
}
