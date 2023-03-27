<?php

namespace App\Console\Commands;

use App\Models\Film;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TicketType;
use App\Models\User;
use App\UseCases\CreateExhibitionUseCase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user';
    protected $description = 'Command description';

    public const WARNING_MSG = 'This command is not suitable for production. Are you sure you want to continue?';

    public function handle()
    {
        User::factory()->create();
    }
}
