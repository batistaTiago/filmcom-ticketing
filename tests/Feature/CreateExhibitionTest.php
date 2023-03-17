<?php

namespace Tests\Feature;

use App\Jobs\CreateExhibitionSeatAvailabilityJob;
use App\Models\Exhibition;
use App\Models\Film;
use App\Models\SeatStatus;
use App\Models\SeatType;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CreateExhibitionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        SeatStatus::factory()->create(['name' => SeatStatus::DEFAULT]);
    }

    /**
     * @test
     * @dataProvider validExhibitionData
     */
    public function should_be_able_to_create_an_exhibition_of_a_film_in_a_theater_room($sampleData)
    {
        Bus::fake();

        Film::factory()->create(['uuid' => $sampleData['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $sampleData['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertCreated();
        $this->assertDatabaseHas('exhibitions', $sampleData);

        Bus::assertDispatchedTimes(CreateExhibitionSeatAvailabilityJob::class, 1);
    }

    /**
     * @test
     * @dataProvider validExhibitionData
     */
    public function should_populate_the_seat_statuses_for_a_newly_created_exhibition($sampleData)
    {
        Film::factory()->create(['uuid' => $sampleData['film_id']]);
        $room = TheaterRoom::factory()->create(['uuid' => $sampleData['theater_room_id']]);

        $rows = TheaterRoomRow::factory()->times(3)->create([
            'theater_room_id' => $room->uuid
        ]);

        $seatType = SeatType::factory()->create();

        foreach ($rows as $row) {
            TheaterRoomSeat::factory()->times(4)->create([
                'theater_room_row_id' => $row->uuid,
                'seat_type_id' => $seatType->uuid
            ]);
        }

        $this->assertDatabaseCount('theater_room_seats', 12);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertCreated();

        $this->assertDatabaseCount('exhibition_seats', TheaterRoomSeat::query()->count());
    }

    /**
     * @test
     * @dataProvider invalidExhibitionData
     */
    public function should_validate_the_data_before_inserting_the_data($sampleData)
    {
        Film::factory()->create(['uuid' => $sampleData['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $sampleData['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertBadRequest();
        $this->assertDatabaseMissing('exhibitions', $sampleData);
        $this->assertDatabaseCount('exhibitions', 0);
    }

    /**
     * @test
     * @dataProvider exhibitionConflictValidationData
     */
    public function should_reject_the_exhibition_if_a_session_time_conflict_is_found(
        $duration,
        $starts_at,
        $is_active,
        $expectedStatus,
    ) {
        $times = collect(['10:00', '15:00', '19:00']);
        $room = TheaterRoom::factory()->create();
        $films = Film::factory()->times(3)->create(['duration' => 120]);

        $times->map(function ($time, $index) use ($room, $films, $is_active) {
            return Exhibition::factory()->create([
                'film_id' => $films[$index]->uuid,
                'theater_room_id' => $room->uuid,
                'starts_at' => Carbon::parse($time),
                'day_of_week' => CarbonInterface::SUNDAY,
                'is_active' => $is_active
            ]);
        });

        $newFilm = Film::factory()->create(compact('duration'));

        $this->postJson(route('api.exhibitions.create'), [
            'film_id' => $newFilm->uuid,
            'theater_room_id' => $room->uuid,
            'starts_at' => $starts_at,
            'day_of_week' => CarbonInterface::SUNDAY,
            'is_active' => true,
        ])
            ->assertStatus($expectedStatus);
    }

    public static function validExhibitionData(): array
    {
        return [
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],

            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => '16:45',
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => '17:00',
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => '10:20',
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],

            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
            ],
        ];
    }

    public static function invalidExhibitionData(): array
    {
        return [
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->dateTime,
                    'day_of_week' => 0,
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => -1,
                    'is_active' => true,
                ],
            ],
            [
                [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => 10,
                    'is_active' => true,
                ],
            ]
        ];
    }

    public static function exhibitionConflictValidationData(): array
    {
        return [
            [120, '07:00', true, 201],
            [120, '12:30', true, 201],
            [120, '21:30', true, 201],

            [30, '09:59', true, 400],
            [180, '14:59', true, 400],
            [600, '14:30', true, 400],
            [30, '19:00', true, 400],

            [30, '09:59', false, 201],
            [180, '14:59', false, 201],
            [600, '14:30', false, 201],
        ];
    }
}
