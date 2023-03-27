<?php

namespace Domain\DTO;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatDTO;
use App\Domain\DTO\TheaterRoom\TheaterRoomSeatTypeDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\TicketTypeDTO;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class TicketDTOTest extends TestCase
{
    /**
     * @test
     * @dataProvider validTicketDataProvider
     */
    public function should_create_a_ticket_instance(string $uuid, TheaterRoomSeatDTO $seat = null, ExhibitionDTO $exhibition = null, TicketTypeDTO $type = null)
    {
        $ticket = new TicketDTO($uuid, $seat, $exhibition, $type);
        $this->assertInstanceOf(TicketDTO::class, $ticket);
        $this->assertEquals($uuid, $ticket->uuid);
        $this->assertEquals($seat, $ticket->seat);
        $this->assertEquals($exhibition, $ticket->exhibition);
        $this->assertEquals($type, $ticket->type);
    }

    public static function validTicketDataProvider()
    {
        return [
            [
                '1234',
                null,
                null,
                null
            ],

            [
                '1234',
            ],
            [
                '5678',
                new TheaterRoomSeatDTO('9876', 'Seat A1', new TheaterRoomSeatTypeDTO('arbitrary-uuid', 'A')),
                new ExhibitionDTO('4567', 'Film A', 'Room 1', '2023-03-24 14:00:00', 1, true),
                new TicketTypeDTO('1111', 'Regular')
            ],
            [
                '9101',
                new TheaterRoomSeatDTO('1212', 'Seat B1', new TheaterRoomSeatTypeDTO('arbitrary-uuid', 'B')),
                new ExhibitionDTO('1313', 'Film B', 'Room 2', '2023-03-24 20:00:00', 3, true),
                new TicketTypeDTO('1414', 'VIP')
            ],
            [
                '1516',
                new TheaterRoomSeatDTO('1617', 'Seat C1', new TheaterRoomSeatTypeDTO('arbitrary-uuid', 'C')),
                new ExhibitionDTO('1819', 'Film C', 'Room 3', '2023-03-25 16:00:00', 5, false),
                new TicketTypeDTO('2021', 'Student')
            ],
            [
                '2223',
                new TheaterRoomSeatDTO('2324', 'Seat D1', new TheaterRoomSeatTypeDTO('arbitrary-uuid', 'D')),
                new ExhibitionDTO('2526', 'Film D', 'Room 4', '2023-03-25 22:00:00', 7, true),
                new TicketTypeDTO('2728', 'Senior')
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidDataProvider
     */
    public function should_throw_exception_if_ticket_data_is_invalid($uuid, $seat, $exhibition, $type)
    {
        $this->expectException(Throwable::class);
        $ticket = new TicketDTO($uuid, $seat, $exhibition, $type);
    }

    public static function invalidDataProvider()
    {
        return [
            // missing uuid
            ['', null, null, null],
            // invalid seat object
            ['abc', new stdClass(), null, null],
            // invalid exhibition object
            ['abc', null, new stdClass(), null],
            // invalid ticket type object
            ['abc', null, null, new stdClass()],
        ];
    }
}
