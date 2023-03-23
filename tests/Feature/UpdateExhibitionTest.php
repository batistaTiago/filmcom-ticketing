<?php

namespace Tests\Feature;

use App\Models\Exhibition;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Tests\TestCase;

class UpdateExhibitionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2023, 03, 22));
    }

    /** @test */
    public function should_return_not_found_if_no_exhibition_is_found(): void
    {
        $this->patch(route('api.exhibitions.update', fake()->uuid), ['is_active' => false])
            ->assertNotFound();

        $this->assertDatabaseCount('exhibitions', 0);
    }

    /** @test */
    public function should_not_update_the_film_and_theater_room_ids(): void
    {
        $exhibition = Exhibition::factory()->create();

        $patchedData = array_merge($exhibition->toArray(), [
            'film_id' => fake()->uuid,
            'theater_room_id' => fake()->uuid
        ]);

        $this->patch(route('api.exhibitions.update', $exhibition->uuid), $patchedData)
            ->assertOk();

        $this->assertDatabaseHas('exhibitions', $exhibition->toArray());
    }

    /**
     * @test
     * @dataProvider exhibitionUpdateDataProvider
     */
    public function should_update_an_exhibition($initialData, $updatedData): void
    {
        $exhibition = Exhibition::factory()->create($initialData);

        $patchedData = array_merge($exhibition->toArray(), $updatedData);

        $this->patch(route('api.exhibitions.update', $exhibition->uuid), $patchedData)
            ->assertOk();

        $this->assertDatabaseHas('exhibitions', $patchedData);
    }

    public static function exhibitionUpdateDataProvider(): array
    {
        return [
            [
                ['starts_at' => '10:00'],
                ['starts_at' => '11:00'],
            ],
            [
                ['is_active' => true],
                ['is_active' => false],
            ],
            [
                ['day_of_week' => CarbonInterface::SUNDAY],
                ['day_of_week' => CarbonInterface::WEDNESDAY],
            ],
            [
                ['starts_at' => '10:00', 'is_active' => true],
                ['starts_at' => '11:00', 'is_active' => false],
            ],
            [
                ['starts_at' => '10:00', 'is_active' => true],
                ['starts_at' => '11:00', 'is_active' => false],
            ],
            [
                ['starts_at' => '10:00', 'is_active' => true],
                ['starts_at' => '11:00', 'is_active' => false],
            ],
            [
                ['starts_at' => '16:00', 'day_of_week' => CarbonInterface::SUNDAY],
                ['starts_at' => '16:30', 'day_of_week' => CarbonInterface::WEDNESDAY],
            ],
        ];
    }
}
