<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'users:create';
    protected $description = 'Command description';

    public const WARNING_MSG = 'This command is not suitable for production. Are you sure you want to continue?';

    public function handle()
    {
        User::factory()->create();
    }
}
