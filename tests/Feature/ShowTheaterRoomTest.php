<?php

namespace Tests\Feature;

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
        $rows = TheaterRoomRow::factory()->times(rand(2, 3))->create([
            'theater_room_id' => $room->uuid
        ]);

        foreach ($rows as $row) {
            TheaterRoomSeat::factory()->times(rand(3, 4))->create([
                'theater_room_row_id' => $row->uuid,
                'seat_type_id' => $seatType->uuid
            ]);
        }

        $this->get(route('api.theater-rooms.show', $room->uuid))
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
            ]);
    }
}
