<?php

namespace Tests\Feature\Repositories;

use App\Models\Exhibition;
use App\Models\TheaterRoomSeat;
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Domain\Repositories\ExhibitionSeatRepositoryInterface;
use Tests\TestCase;

class MysqlExhibitionSeatRepositoryTest extends TestCase
{
    /**
     * @dataProvider changeSeatStatusBatchDataProvider
     */
    public function testChangeSeatStatusBatch(array $theaterRoomSeatIds, string $newSeatStatusName): void
    {
        $exhibition = Exhibition::factory()->create();
        $theaterRoomSeats = collect();
        foreach ($theaterRoomSeatIds as $theaterRoomSeatId) {
            $theaterRoomSeats[] = TheaterRoomSeat::factory()->create([
                'uuid' => $theaterRoomSeatId
            ]);
        }

        $initialSeatStatus = SeatStatus::factory()->create(['name' => 'initial']);
        $newSeatStatus = SeatStatus::factory()->create(['name' => $newSeatStatusName]);

        foreach ($theaterRoomSeats as $theaterRoomSeat) {
            ExhibitionSeat::factory()->create([
                'exhibition_id' => $exhibition->uuid,
                'theater_room_seat_id' => $theaterRoomSeat->uuid,
                'seat_status_id' => $initialSeatStatus->uuid,
            ]);
        }

        $exhibitionSeatRepository = $this->app->make(ExhibitionSeatRepositoryInterface::class);
        $exhibitionSeatRepository->changeSeatStatusBatch($exhibition->uuid, $theaterRoomSeatIds, $newSeatStatus->uuid);

        foreach ($theaterRoomSeatIds as $theaterRoomSeatId) {
            $this->assertDatabaseHas('exhibition_seats', [
                'exhibition_id' => $exhibition->uuid,
                'theater_room_seat_id' => $theaterRoomSeatId,
                'seat_status_id' => $newSeatStatus->uuid,
            ]);
        }
    }

    public static function changeSeatStatusBatchDataProvider(): array
    {
        return [
            [[fake()->uuid, fake()->uuid, fake()->uuid], 'reserved'],
            [[fake()->uuid, fake()->uuid], 'sold'],
            [[fake()->uuid], 'available'],
        ];
    }
}
