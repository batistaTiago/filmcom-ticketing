<?php

namespace Database\Factories;

use App\Models\Exhibition;
use App\Models\SeatStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitionSeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'exhibition_id' => Exhibition::factory(),
            'seat_status_id' => SeatStatus::factory(),
        ];
    }
}
