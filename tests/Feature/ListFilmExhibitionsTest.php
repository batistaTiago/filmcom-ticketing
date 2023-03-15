<?php

namespace Tests\Feature;

use App\Models\Exhibition;
use App\Models\Film;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ListFilmExhibitionsTest extends TestCase
{
    private Film $film;
    private Theater $theater;
    private array $rooms;
    private array $rows;
    private array $seats;

    private const EXPECTED_EXHIBITION_COUNT = 10;

    public function setUp(): void
    {
        parent::setUp();

        $this->film = Film::factory()->create();
        $this->theater = Theater::factory()->create();
        $this->rooms = TheaterRoom::factory()->times(2)->create(['theater_id' => $this->theater->uuid])->toArray();

        foreach ($this->rooms as $room) {
            $this->rows[$room['uuid']] = TheaterRoomRow::factory()->times(3)->create([
                'theater_room_id' => $room['uuid']
            ])->toArray();

            foreach ($this->rows[$room['uuid']] as $row) {
                $this->seats[$room['uuid']][$row['uuid']] = TheaterRoomSeat::factory()->times(4)->create([
                    'theater_room_row_id' => $row['uuid']
                ])->toArray();
            }
        }

        Exhibition::factory()->times(10)->create([
            'film_id' => $this->film->uuid,
            'theater_room_id' => Arr::random($this->rooms)['uuid']
        ]);

        Exhibition::factory()->times(10)->create([
            'theater_room_id' => Arr::random($this->rooms)['uuid']
        ]);
    }

    /** @test */
    public function should_list_all_the_exhibitions_for_a_film()
    {
        $url = route('api.film_exhibitions.index') . '?' . http_build_query(['film_id' => $this->film->uuid]);
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

        $this->assertCount(10, $res);
    }
}
