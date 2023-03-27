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
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateExhibitionCommand extends Command
{
    protected $signature = 'exhibition:create';
    protected $description = 'Command description';

    public const WARNING_MSG = 'This command is not suitable for production. Are you sure you want to continue?';

    public function handle(CreateExhibitionUseCase $useCase)
    {
        if ((strtolower(config('app.env')) === 'production') && (!$this->confirm(self::WARNING_MSG))) {
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

        $startsAt = $this->ask('Whats the time of the exhibition?');

        $validator = validator(['starts_at' => $startsAt], [
            'starts_at' => [
                'required',
                'string',
                'date_format:H:i'
            ],
        ]);

        if ($validator->fails()) {
            $this->error('Invalid time format. Please use H:i (i.e. 08:30) format.');
            return Command::INVALID;
        }

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

        return Command::SUCCESS;
    }
}
