<?php


use App\Models\Theater;
use Tests\TestCase;

class CreateTheaterRoomTest extends TestCase
{
    /** @test */
    public function should_be_able_to_create_a_theater_room()
    {
        $theater = Theater::factory()->create(['name' => 'FilmCOM - Vancouver']);
        $roomName = 'Room A';

        $this->postJson(route('api.theater-rooms.create'), [
            'theater_id' => $theater->uuid,
            'name' => $roomName,
        ])->assertCreated();

        $this->assertDatabaseCount('theater_rooms', 1);
        $this->assertDatabaseHas('theater_rooms', [
            'name' => $roomName,
            'theater_id' => $theater->uuid,
        ]);
    }

    /** @test */
    public function should_require_a_name_in_the_request_body()
    {
        $theater = Theater::factory()->create(['name' => 'FilmCOM - Vancouver']);

        $this->postJson(route('api.theater-rooms.create'), [
            'theater_id' => $theater->uuid,
        ])->assertBadRequest();

        $this->assertDatabaseCount('theater_rooms', 0);
    }

    /** @test */
    public function should_require_a_valid_theater_id_in_the_request_body()
    {
        $roomName = 'Room A';

        $this->postJson(route('api.theater-rooms.create'), [
            'name' => $roomName,
        ])->assertBadRequest();

        $this->assertDatabaseCount('theater_rooms', 0);
    }
}
