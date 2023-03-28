<?php

namespace Tests\Feature\Commands;

use App\Jobs\CreateExhibitionSeatAvailabilityJob;
use App\Jobs\PopulateExhibitionTicketPricingJob;
use App\Models\Film;
use App\Models\SeatType;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\TicketType;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;
use Database\Seeders\BasicRequiredDataSeeder;

class CreateExhibitionCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(BasicRequiredDataSeeder::class);
    }

    /**
     * @dataProvider commandDataProvider
     */
    public function testCreateExhibitionCommand($appEnv, $expectEnvWarning, $filmData, $theaterData, $roomData, $startsAt, $dayOfWeek)
    {
        Bus::fake();

        Config::set('app.env', $appEnv);

        $film = Film::factory()->create($filmData);
        $theater = Theater::factory()->create($theaterData);
        $room = TheaterRoom::factory()->create(array_merge($roomData, ['theater_id' => $theater->uuid]));

        $seatType = SeatType::firstWhere(['name' => SeatType::REGULAR]);

        $rowCount = rand(2, 3);
        $seatCount = rand(3, 4);

        for ($i = 0; $i < $rowCount; $i++) {
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

        $ticketTypes = TicketType::all();

        $command = $this->artisan('exhibitions:create');

        if ($expectEnvWarning) {
            $command->expectsQuestion('This command is not suitable for production. Are you sure you want to continue?', 'yes');
        }

        $command->expectsQuestion('Whats the film?', $film->name)
            ->expectsQuestion('Whats the theater?', $theater->name)
            ->expectsQuestion('Whats the theater room?', $room->name)
            ->expectsQuestion('Whats the time of the exhibition?', $startsAt)
            ->expectsQuestion('Which day of the week is it?', $dayOfWeek)
            ->expectsQuestion('What ticket types do you wanna add to the exhibition?', [$ticketTypes->first()->name])
            ->expectsQuestion("Whats the price for the {$ticketTypes->first()->name} ticket? (in cents)", $ticketTypes->first()->price);

        $exitCode = $command->run();
        $this->assertEquals(0, $exitCode);

        Bus::assertDispatchedTimes(CreateExhibitionSeatAvailabilityJob::class, 1);
        Bus::assertDispatchedTimes(PopulateExhibitionTicketPricingJob::class, 1);
    }

    public static function commandDataProvider()
    {
        return [
            [
                'local',
                false,
                ['name' => 'Film 1'],
                ['name' => 'Theater 1'],
                ['name' => 'Room 1'],
                '14:00',
                'Monday',
            ],
            [
                'production',
                true,
                ['name' => 'Film 2'],
                ['name' => 'Theater 2'],
                ['name' => 'Room 2'],
                '16:30',
                'Wednesday',
            ],
            [
                'local',
                false,
                ['name' => 'Film 3'],
                ['name' => 'Theater 3'],
                ['name' => 'Room 3'],
                '19:45',
                'Friday',
            ],
            [
                'production',
                true,
                ['name' => 'Film 4'],
                ['name' => 'Theater 4'],
                ['name' => 'Room 4'],
                '00:00',
                'Monday',
            ],
            [
                'local',
                false,
                ['name' => 'Film 5'],
                ['name' => 'Theater 5'],
                ['name' => 'Room 5'],
                '14:30',
                'Tuesday',
            ],
            [
                'production',
                true,
                ['name' => 'Film 6'],
                ['name' => 'Theater 6'],
                ['name' => 'Room 6'],
                '23:59',
                'Sunday',
            ],
        ];
    }

    /**
     * @dataProvider invalidTimeDataProvider
     */
    public function testInvalidTimeValidation($invalidTime)
    {
        $film = Film::factory()->create(['name' => 'Film 1']);
        $theater = Theater::factory()->create(['name' => 'Theater 1']);
        $room = TheaterRoom::factory()->create(array_merge(['name' => 'Room 1'], ['theater_id' => $theater->uuid]));

        $this->artisan('exhibitions:create')
            ->expectsQuestion('Whats the film?', $film->name)
            ->expectsQuestion('Whats the theater?', $theater->name)
            ->expectsQuestion('Whats the theater room?', $room->name)
            ->expectsQuestion('Whats the time of the exhibition?', $invalidTime)
            ->expectsOutputToContain('Invalid')
            ->assertExitCode(Command::INVALID);
    }

    public static function invalidTimeDataProvider()
    {
        return [
            ['24:00'],
            ['15:60'],
            ['25:30'],
            ['12:3'],
            ['invalid time'],
        ];
    }
}
