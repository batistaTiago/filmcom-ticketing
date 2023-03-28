<?php

namespace App\Services;

use App\Domain\DTO\ExhibitionDTO;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Error;
use Illuminate\Support\Str;

// TODO remove these imports after properly layering this service
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Models\TheaterRoomRow;
use Throwable;

class PopulateExhibitionSeatsService
{
    public function __construct(private readonly Connection $databaseConnection)
    {
    }

    public function execute(ExhibitionDTO $exhibition)
    {
        try {
            // TODO layerize this code
            $this->databaseConnection->beginTransaction();
            $rows = TheaterRoomRow::with('seats')->where('theater_room_id', $exhibition->theater_room_id)->get();
            $defaultSeatStatus = SeatStatus::where(['name' => SeatStatus::DEFAULT])->first();

            if (empty($defaultSeatStatus)) {
                throw new Error('The default seat status has not been setup.');
            }

            ExhibitionSeat::query()->insert($this->getInsertData($rows, $defaultSeatStatus, $exhibition));
            $this->databaseConnection->commit();
        } catch (Throwable $e) {
            $this->databaseConnection->rollBack();
            throw $e;
        }
    }

    private function getInsertData(Collection $rows, SeatStatus $defaultSeatStatus, ExhibitionDTO $exhibition): array {
        $insertData = [];

        foreach ($rows as $row) {
            foreach ($row->seats as $seat) {
                $insertData[] = [
                    'uuid' => Str::orderedUuid()->toString(),
                    'exhibition_id' => $exhibition->uuid,
                    'theater_room_seat_id' => $seat->uuid,
                    'seat_status_id' => $defaultSeatStatus->uuid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }


        return $insertData;
    }
}
