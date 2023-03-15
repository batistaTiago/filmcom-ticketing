<?php

namespace Tests\Feature;

use App\Models\Film;
use Tests\TestCase;

class ListFilmsTest extends TestCase
{
    /** @test */
    public function should_return_a_paginated_films_list()
    {
        Film::factory()->times(100)->create();

        $responseData = $this->get(route('api.films.index'))
            ->assertOk()
            ->decodeResponseJson();

        $this->assertCount(10, $responseData['data']);
        $this->assertEquals(10, $responseData['last_page']);
    }

    /** @test */
    public function should_return_an_filtered_films_list()
    {
        $year = 1999;
        Film::factory()->times(50)->create(['year' => fake()->numberBetween(2000, 2010)]);
        Film::factory()->times(50)->create(compact('year'));

        $url = route('api.films.index') . '?' . http_build_query(compact('year'));

        $responseData = $this->get($url)
            ->assertOk()
            ->decodeResponseJson();

        $this->assertCount(10, $responseData['data']);
        $this->assertEquals(5, $responseData['last_page']);
    }

    /** @test */
    public function should_return_an_empty_films_list_if_no_one_is_found()
    {
        $responseData = $this->get(route('api.films.index'))
            ->assertOk()
            ->decodeResponseJson();

        $this->assertCount(0, $responseData['data']);
        $this->assertEquals(1, $responseData['last_page']);
    }
}
