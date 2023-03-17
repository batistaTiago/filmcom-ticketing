<?php

namespace Tests\Feature;

use App\Models\Exhibition;
use Tests\TestCase;

class UpdateExhibitionTest extends TestCase
{
    /** @test */
    public function should_update_an_exhibition(): void
    {
        $exhibition = Exhibition::factory()->create(['starts_at' => '11:00']);
//        dd(route('api.exhibitions.update', $exhibition->uuid));

        $patchedData = array_merge($exhibition->toArray(), ['starts_at' => '10:00']);

        $this->patch(route('api.exhibitions.update', $exhibition->uuid), $patchedData)
            ->assertOk()
            ->decodeResponseJson();

        $this->assertDatabaseHas('exhibitions', $patchedData);
    }
}
