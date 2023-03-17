<?php

namespace Database\Factories;

use App\Models\Film;
use App\Models\TheaterRoom;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExhibitionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'film_id' => Film::factory(),
            'theater_room_id' => TheaterRoom::factory(),
            'starts_at' => fake()->time,
            'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
            'is_active' => true,
        ];
    }
}
