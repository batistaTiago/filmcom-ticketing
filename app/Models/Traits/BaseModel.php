<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

trait BaseModel
{
    use HasFactory;

    public function toArray(): array
    {
        return array_map(function ($attribute) {
            if ($attribute instanceof Model) {
                return $attribute->toArray();
            }

            return $attribute;
        }, parent::toArray());
    }
}
