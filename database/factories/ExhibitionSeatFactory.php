<?php

namespace Database\Factories;

use App\Models\Exhibition;
use App\Models\SeatStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExhibitionSeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'exhibition_id' => Exhibition::factory(),
            'seat_status_id' => SeatStatus::factory(),
        ];
    }
}
