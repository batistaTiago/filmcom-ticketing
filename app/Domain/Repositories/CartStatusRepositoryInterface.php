<?php

namespace App\Domain\Repositories;

use App\Domain\DTO\Cart\CartStatusDTO;

interface CartStatusRepositoryInterface
{
    public function getByName(string $name): CartStatusDTO;
}
