<?php

namespace Tests\Feature;

use App\Jobs\CreateExhibitionSeatAvailabilityJob;
use App\Jobs\PopulateExhibitionTicketPricingJob;
use App\Models\Exhibition;
use App\Models\Film;
use App\Models\SeatStatus;
use App\Models\SeatType;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\TicketType;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
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
        Film::factory()->create(['uuid' => $sampleData['exhibition']['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $sampleData['exhibition']['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertCreated();
        $this->assertDatabaseHas('exhibitions', $sampleData['exhibition']);
    }

    /**
     * @test
     * @dataProvider validExhibitionData
     */
    public function should_dispatch_jobs_to_populate_seat_availability_and_ticket_prices($sampleData)
    {
        Bus::fake();

        Film::factory()->create(['uuid' => $sampleData['exhibition']['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $sampleData['exhibition']['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertCreated();

        Bus::assertDispatchedTimes(CreateExhibitionSeatAvailabilityJob::class, 1);
        Bus::assertDispatchedTimes(PopulateExhibitionTicketPricingJob::class, 1);
    }

    /**
     * @test
     * @dataProvider validExhibitionData
     */
    public function should_populate_the_seat_statuses_on_exhibition_creation($sampleData)
    {
        Film::factory()->create(['uuid' => $sampleData['exhibition']['film_id']]);
        $room = TheaterRoom::factory()->create(['uuid' => $sampleData['exhibition']['theater_room_id']]);
        $seatType = SeatType::factory()->create();

        $rowCount = rand(2, 3);
        $seatCount = rand(3, 4);

        for ($i = 0; $i < $rowCount; $i++) {
            $row = TheaterRoomRow::factory()->create([
                'name' => Str::orderedUuid()->toString(),
                'theater_room_id' => $room->uuid
            ]);

            for ($j = 0; $j < $seatCount; $j++) {

                TheaterRoomSeat::factory()->create([
                    'name' => Str::orderedUuid()->toString(),
                    'theater_room_row_id' => $row->uuid,
                    'seat_type_id' => $seatType->uuid
                ]);
            }
        }

        $this->assertDatabaseCount('theater_room_seats', $seatCount * $rowCount);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertCreated();

        $this->assertDatabaseCount('exhibition_seats', TheaterRoomSeat::query()->count());
    }

    /**
     * @test
     */
    public function should_not_populate_the_available_ticket_types_if_no_one_is_provided()
    {
        $sampleData = [
            'exhibition' => [
                'film_id' => fake()->uuid,
                'theater_room_id' => fake()->uuid,
                'starts_at' => fake()->time,
                'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                'is_active' => true,
            ],
        ];

        TicketType::factory()->create([
            'name' => TicketType::REGULAR
        ]);

        Film::factory()->create(['uuid' => $sampleData['exhibition']['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $sampleData['exhibition']['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), $sampleData)
            ->assertCreated()
            ->decodeResponseJson();

        $this->assertDatabaseCount('exhibition_ticket_types', 0);
    }

    /**
     * @test
     * @dataProvider exhibitionWithTicketTypes
     */
    public function should_populate_the_selected_ticket_type_prices_on_exhibition_creation($exhibitionData, $ticketTypesData)
    {
        $ticketTypesParam = [];
        foreach ($ticketTypesData as $ticketTypeData) {
            TicketType::factory()->create([
                'uuid' => $ticketTypeData['uuid'],
                'name' => $ticketTypeData['name'],
            ]);

            $ticketTypesParam[] = [
                'uuid' => $ticketTypeData['uuid'],
                'price' => $ticketTypeData['price'],
            ];
        }

        Film::factory()->create(['uuid' => $exhibitionData['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $exhibitionData['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), [
            'exhibition' => $exhibitionData,
            'ticket_types' => $ticketTypesParam
        ])
            ->assertCreated()
            ->decodeResponseJson();

        foreach ($ticketTypesData as $ticketTypeData) {
            $this->assertDatabaseHas('exhibition_ticket_types', [
                'ticket_type_id' => $ticketTypeData['uuid']
            ]);
        }
    }

    /**
     * @test
     * @dataProvider invalidExhibitionData
     */
    public function should_validate_the_data_before_inserting_the_data($sampleData)
    {
        Film::factory()->create(['uuid' => $sampleData['exhibition']['film_id']]);
        TheaterRoom::factory()->create(['uuid' => $sampleData['exhibition']['theater_room_id']]);

        $this->postJson(route('api.exhibitions.create'), $sampleData)->assertBadRequest();
        $this->assertDatabaseMissing('exhibitions', $sampleData['exhibition']);
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
            'exhibition' => [
                'film_id' => $newFilm->uuid,
                'theater_room_id' => $room->uuid,
                'starts_at' => $starts_at,
                'day_of_week' => CarbonInterface::SUNDAY,
                'is_active' => true,
            ],
        ])
            ->assertStatus($expectedStatus);
    }

    public static function validExhibitionData(): array
    {
        return [
            [
                [
                    'exhibition' => [
                        'film_id' => fake()->uuid,
                        'theater_room_id' => fake()->uuid,
                        'starts_at' => fake()->time,
                        'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                        'is_active' => true,
                    ],
                ],
            ],
            [
                [
                    'exhibition' => [
                        'film_id' => fake()->uuid,
                        'theater_room_id' => fake()->uuid,
                        'starts_at' => fake()->time,
                        'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                        'is_active' => true,
                    ],
                ],
            ],
            [
                [
                    'exhibition' => [
                        'film_id' => fake()->uuid,
                        'theater_room_id' => fake()->uuid,
                        'starts_at' => fake()->time,
                        'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                        'is_active' => false,
                    ],
                ],
            ],
        ];
    }

    public static function invalidExhibitionData(): array
    {
        return [
            [
                [
                    'exhibition' => [
                        'film_id' => fake()->uuid,
                        'theater_room_id' => fake()->uuid,
                        'starts_at' => fake()->dateTime,
                        'day_of_week' => 0,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                [
                    'exhibition' => [
                        'film_id' => fake()->uuid,
                        'theater_room_id' => fake()->uuid,
                        'starts_at' => fake()->time,
                        'day_of_week' => -1,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                [
                    'exhibition' => [
                        'film_id' => fake()->uuid,
                        'theater_room_id' => fake()->uuid,
                        'starts_at' => fake()->time,
                        'day_of_week' => 10,
                        'is_active' => true,
                    ],
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

    public static function exhibitionWithTicketTypes(): array
    {
        return [
            [
                'exhibition_data' => [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
                'ticket_type_data' => []
            ],
            [
                'exhibition_data' => [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
                'ticket_type_data' => [
                    [
                        'uuid' => fake()->uuid,
                        'name' => TicketType::REGULAR,
                        'price' => 4000,
                    ],
                ]
            ],
            [
                'exhibition_data' => [
                    'film_id' => fake()->uuid,
                    'theater_room_id' => fake()->uuid,
                    'starts_at' => fake()->time,
                    'day_of_week' => fake()->numberBetween(CarbonInterface::SUNDAY, CarbonInterface::SATURDAY),
                    'is_active' => true,
                ],
                'ticket_type_data' => [
                    [
                        'uuid' => fake()->uuid,
                        'name' => TicketType::REGULAR,
                        'price' => 4000,
                    ],
                    [
                        'uuid' => fake()->uuid,
                        'name' => TicketType::STUDENT,
                        'price' => 2000,
                    ],
                ]
            ],
        ];
    }
}
