<?php

namespace App\Repositories;

use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\CartStatus;
use Illuminate\Support\Facades\Cache;

class MysqlCartStatusRepository implements CartStatusRepositoryInterface
{
    public function getByName(string $name): CartStatusDTO
    {
        return Cache::remember(
            md5("get-cart-status-by-name-$name"),
            now()->addDay(),
            function () use ($name) {
                $status = CartStatus::query()->where(['name' => $name])->first();

                if (!$status) {
                    throw new ResourceNotFoundException("Cart status not found: $name");
                }

                return $status->toDto();
            }
        );
    }
}
