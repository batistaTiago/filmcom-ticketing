<?php

namespace Tests\Feature;

use App\Models\Exhibition;
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Models\SeatType;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Tests\TestCase;

class ShowTheaterRoomTest extends TestCase
{
    /** @test */
    public function should_return_not_found_if_no_room_is_found()
    {
        $this->get(route('api.theater-rooms.show', fake()->uuid))
            ->assertNotFound()
            ->assertJsonStructure([
                'error',
            ]);
    }

    /** @test */
    public function should_return_a_theater_room_with_its_rows_and_seats_if_one_is_found()
    {
        $seatType = SeatType::factory()->create();

        $room = TheaterRoom::factory()->create();
        $rows = TheaterRoomRow::factory()->times(2)->create([
            'theater_room_id' => $room->uuid
        ]);

        $seatCount = 3;

        foreach ($rows as $row) {
            for ($i = 0; $i < $seatCount; $i++) {
                TheaterRoomSeat::factory()->create([
                    'name' => fake()->uuid,
                    'theater_room_row_id' => $row->uuid,
                    'seat_type_id' => $seatType->uuid
                ]);
            }
        }

        $res = $this->get(route('api.theater-rooms.show', $room->uuid))
            ->assertOk()
            ->assertJsonStructure([
                'uuid',
                'name',
                'rows' => [
                    [
                        'name',
                        'seats' => [
                            [
                                'name',
                                'type' => [
                                    'name'
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->decodeResponseJson();

        foreach ($res['rows'] as $row) {
            foreach ($row['seats'] as $seat) {
                $this->assertNotNull($seat['type']['name']);
                $this->assertNull($seat['status']);
            }
        }
    }

    /** @test */
    public function should_return_the_room_map_along_with_the_seat_statuses_for_a_specific_exhibition()
    {
        $seatType = SeatType::factory()->create();
        $defaultSeatStatus = SeatStatus::factory()->create(['name' => SeatStatus::DEFAULT]);

        $room = TheaterRoom::factory()->create();

        $exhibitionCount = 2;
        $rowCount = 2;
        $seatsPerRow = 3;

        $exhibitions = collect();
        $rows = collect();

        for ($i = 0; $i < $exhibitionCount; $i++) {
            $exhibitions[] = Exhibition::factory()->create([
                'theater_room_id' => $room->uuid,
            ]);
        }

        for ($i = 0; $i < $rowCount; $i++) {
            $rows[] = TheaterRoomRow::factory()->create([
                'name' => fake()->uuid,
                'theater_room_id' => $room->uuid,
            ]);
        }

        foreach ($rows as $row) {
            $seats = [];

            for ($i = 0; $i < $seatsPerRow; $i++) {
                $seats[] = TheaterRoomSeat::factory()->create([
                    'name' => fake()->uuid,
                    'theater_room_row_id' => $row->uuid,
                    'seat_type_id' => $seatType->uuid
                ]);
            }

            foreach ($exhibitions as $exhibition) {
                foreach ($seats as $seat) {
                    ExhibitionSeat::factory()->create([
                        'exhibition_id' => $exhibition->uuid,
                        'theater_room_seat_id' => $seat->uuid,
                        'seat_status_id' => $defaultSeatStatus->uuid,
                    ]);
                }
            }
        }

        $res = $this->get(route('api.theater-rooms.show-availability', [$exhibitions->first()->uuid]))
            ->assertOk()
            ->assertJsonStructure([
                'uuid',
                'name',
                'rows' => [
                    [
                        'name',
                        'seats' => [
                            [
                                'name',
                                'type' => [
                                    'name'
                                ],
                                'status' => [
                                    'name'
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->decodeResponseJson();

        foreach ($res['rows'] as $row) {
            foreach ($row['seats'] as $seat) {
                $this->assertNotNull($seat['type']['name']);
                $this->assertNotNull($seat['status']['name']);
            }
        }
    }
}
