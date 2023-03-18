<?php

namespace Tests\Feature;

use App\Models\Exhibition;
use App\Models\Film;
use App\Models\SeatType;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ListFilmExhibitionsTest extends TestCase
{
    private const EXPECTED_EXHIBITION_COUNT = 10;

    /** @test */
    public function should_list_all_the_exhibitions_for_a_film()
    {

        $film = Film::factory()->create();
        $theater = Theater::factory()->create();
        $rooms = TheaterRoom::factory()->times(2)->create(['theater_id' => $theater->uuid]);

        $seatType = SeatType::factory()->create();

        foreach ($rooms as $room) {
            $rows[$room->uuid] = TheaterRoomRow::factory()->times(3)->create([
                'theater_room_id' => $room->uuid
            ]);

            foreach ($rows[$room->uuid] as $row) {
                TheaterRoomSeat::factory()->times(4)->create([
                    'theater_room_row_id' => $row->uuid,
                    'seat_type_id' => $seatType->uuid
                ]);
            }
        }

        Exhibition::factory()->times(10)->create([
            'film_id' => $film->uuid,
            'theater_room_id' => $rooms->random()->uuid
        ]);

        Exhibition::factory()->times(10)->create([
            'theater_room_id' => $rooms->random()->uuid
        ]);

        $url = route('api.film_exhibitions.index') . '?' . http_build_query(['film_id' => $film->uuid]);
        $res = $this->get($url)
            ->assertOk()
            ->assertJsonStructure([
                [
                    'uuid',
                    'film_id',
                    'theater_room_id',
                    'starts_at',
                    'day_of_week',
                    'is_active'
                ]
            ])
            ->decodeResponseJson();

        $this->assertCount(self::EXPECTED_EXHIBITION_COUNT, $res);
    }
}
