<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateTheaterTest extends TestCase
{
    /** @test */
    public function should_be_able_to_create_a_theater()
    {
        $theaterName = 'FilmCOM - Vancouver';

        $this->postJson(route('api.theaters.create'), [
            'name' => $theaterName
        ]);

        $this->assertDatabaseCount('theaters', 1);
        $this->assertDatabaseHas('theaters', [ 'name' => $theaterName ]);
    }

    /** @test */
    public function should_require_a_name_in_the_request_body()
    {
        $theaterName = '';

        $this->postJson(route('api.theaters.create'), [
            'name' => $theaterName
        ]);

        $this->assertDatabaseCount('theaters', 0);
    }
}
