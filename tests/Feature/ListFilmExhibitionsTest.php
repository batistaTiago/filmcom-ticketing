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
use Illuminate\Support\Str;
use Tests\TestCase;

class ListFilmExhibitionsTest extends TestCase
{
    private const EXPECTED_EXHIBITION_COUNT = 10;

    /** @test */
    public function should_list_all_the_exhibitions_for_a_film()
    {
        $rooms = collect();

        $film = Film::factory()->create();
        $theater = Theater::factory()->create();
        $seatType = SeatType::factory()->create();

        $rowCount = rand(2, 3);
        $seatCount = rand(3, 4);

        for ($i = 0; $i < $rowCount; $i++) {
            $room = TheaterRoom::factory()->create(['theater_id' => $theater->uuid]);
            $rooms[] = $room;

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
