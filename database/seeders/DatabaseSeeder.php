<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ExhibitionSeat;
use App\Models\Film;
use App\Models\SeatStatus;
use App\Models\Theater;
use App\Models\TheaterRoom;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use App\Models\SeatType;
use App\Models\TicketType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(BasicRequiredDataSeeder::class);

        if (config('app.env') === 'production') {
            return;
        }

        $this->call(SeederFakeDataForTesting::class);


        // shuffling seat statuses
//        $seats = ExhibitionSeat::query()->get();
//        $seat_statuses = SeatStatus::query()->where('name', '!=', 'unavailable')->get();
//        $seats->each(function ($seat) use ($seat_statuses) {
//            $seat->update(['seat_status_id' => $seat_statuses->random()->uuid]);
//        });
    }
}
