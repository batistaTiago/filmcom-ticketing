<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
