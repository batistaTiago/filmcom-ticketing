<?php

namespace App\Console\Commands;

use App\Models\Film;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TicketType;
use App\UseCases\CreateExhibitionUseCase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateExhibitionCommand extends Command
{
    protected $signature = 'create:exhibition';
    protected $description = 'Command description';

    public function handle(CreateExhibitionUseCase $useCase)
    {
        if (!$this->confirm('This command is not suitable for production. Are you sure you want to continue?')) {
            return Command::INVALID;
        }

        $films = Film::query()->select('uuid', 'name')->get();
        $theaters = Theater::query()->select('uuid', 'name')->get();

        $filmName = $this->choice('Whats the film?', $films->pluck('name')->toArray());
        $film = $films->where('name', $filmName)->first();
        $theaterName = $this->choice('Whats the theater?', $theaters->pluck('name')->toArray());

        $theater = $theaters->where('name', $theaterName)->first();

        $rooms = TheaterRoom::query()->where('theater_id', $theater->uuid)->get();
        $roomName = $this->choice('Whats the theater room?', $rooms->pluck('name')->toArray());

        $room = $rooms->where('name', $roomName)->first();

        // TODO validate startsAt variable
        $startsAt = $this->ask('Whats the time of the exhibition?');

        $dayOfWeek = array_flip(Carbon::getDays())[$this->choice('Which day of the week is it?', Carbon::getDays())];

        $exhibitionData = [
            "uuid" => Str::orderedUuid()->toString(),
            "film_id" => $film->uuid,
            "theater_room_id" => $room->uuid,
            "starts_at" => $startsAt,
            "day_of_week" => $dayOfWeek,
            "is_active" => true,
        ];

        $ticketTypes = TicketType::query()->select('uuid', 'name')->get();
        $ticketTypesToAdd = $this->choice(
            'What ticket types do you wanna add to the exhibition?',
            $ticketTypes->pluck('name')->toArray(),
            multiple: true
        );

        $ticketTypesToAddData = [];

        foreach ($ticketTypesToAdd as $ticketTypeToAdd) {
            $data = [
                'uuid' => $ticketTypes->where('name', $ticketTypeToAdd)->first()->uuid,
                'price' => (float) $this->ask("Whats the price for the $ticketTypeToAdd ticket? (in cents)")
            ];

            $ticketTypesToAddData[] = $data;
        }

        $exhibition = $useCase->execute($exhibitionData, $ticketTypesToAddData);

        $this->info("Exhibition created: $exhibition->uuid");
    }
}
