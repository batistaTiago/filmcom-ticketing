<?php

namespace Tests\Feature;

use App\Models\Exhibition;
use App\Models\ExhibitionTicketType;
use App\Models\TicketType;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ListTicketTypesTest extends TestCase
{
    /** @test */
    public function should_list_all_ticket_types()
    {
        TicketType::factory()->times(5)->create();

        $res = $this->get(route('api.ticket-types.index'))
            ->assertOk()
            ->decodeResponseJson();

        $this->assertEquals(5, count($res));
    }

    /** @test */
    public function should_list_all_ticket_types_for_an_exhibition()
    {
        $exhibitions = Exhibition::factory()->times(2)->create();
        $ticketTypes = TicketType::factory()->times(3)->create()->pluck('uuid');

        foreach ($exhibitions as $exhibition) {
            foreach ($ticketTypes as $ticketType) {
                ExhibitionTicketType::factory()->create([
                    'exhibition_id' => $exhibition->uuid,
                    'ticket_type_id' => $ticketType
                ]);
            }
        }

        $res = $this->get(route('api.exhibition-ticket-types.index', $exhibitions->random()->uuid))
            ->assertOk()
            ->decodeResponseJson();

        $this->assertEquals(3, count($res));
    }
}
