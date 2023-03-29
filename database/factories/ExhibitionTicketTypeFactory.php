<?php

namespace Database\Factories;

use App\Models\Exhibition;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExhibitionTicketTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'exhibition_id' => Exhibition::factory(),
            'ticket_type_id' => TicketType::factory(),
            'price' => fake()->numberBetween(1000, 9999),
        ];
    }
}
