<?php

namespace App\Services;

use App\Models\SeatType;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class PopulateSeatMapService
{
    public function __construct(private readonly Connection $databaseConnection)
    {
    }

    public function execute(string $theater_room_id, Collection $sheets, bool $shouldRebuildMap): void
    {
        $firstSheet = $sheets->first();

        $seatTypes = $this->getSeatTypeUuids();
        $seatNames = $this->getSeatNamesFromSpreadsheet($firstSheet);
        $rowNames = $this->getRowNamesFromSpreadsheet($firstSheet);

        try {
            $this->databaseConnection->beginTransaction();

            if ($shouldRebuildMap) {
                $this->destroyCurrentMap($theater_room_id);
            }

            $this->buildSeatMap($rowNames, $seatNames, $theater_room_id, $seatTypes, $firstSheet);

            $this->databaseConnection->commit();
        } catch (Throwable $e) {
            $this->databaseConnection->rollBack();
            throw $e;
        }
    }

    private function getSeatTypeUuids(): array
    {
        return [
            'R' => SeatType::query()->where('name', SeatType::REGULAR)->first()->uuid,
            'L' => SeatType::query()->where('name', SeatType::LARGE)->first()->uuid,
            'W' => SeatType::query()->where('name', SeatType::WHEEL_CHAIR)->first()->uuid,
        ];
    }

    private function getSeatNamesFromSpreadsheet(Collection $firstSheetAsCollection): Collection
    {
        return $firstSheetAsCollection->first()->slice(1)->filter(fn ($item) => !empty($item));
    }

    private function getRowNamesFromSpreadsheet(Collection $firstSheetAsCollection): Collection
    {
        return $firstSheetAsCollection->slice(1)->map(fn($row) => $row->first())->filter(fn($item) => !empty($item));
    }

    private function destroyCurrentMap(string $theater_room_id): void
    {
        $rows = TheaterRoomRow::query()->where(compact('theater_room_id'))->get();
        TheaterRoomSeat::query()->whereIn('theater_room_row_id', $rows->pluck('uuid'))->delete();
        TheaterRoomRow::query()->where(compact('theater_room_id'))->delete();
    }

    private function buildSeatMap(
        Collection $rowNames,
        Collection $seatNames,
        string $theater_room_id,
        array $seatTypes,
        Collection $firstSheetAsCollection,
    ): void
    {
        $rowData = [];
        $seats = [];

        foreach ($rowNames as $rowIndex => $rowName) {
            $rowData[] = $createdRow = [
                'uuid' => Str::orderedUuid()->toString(),
                'name' => $rowName,
                'theater_room_id' => $theater_room_id,
            ];

            foreach ($seatNames as $seatIndex => $seatName) {
                $seats[] = [
                    'uuid' => Str::orderedUuid()->toString(),
                    'name' => $seatName,
                    'theater_room_row_id' => $createdRow['uuid'],
                    'seat_type_id' => $seatTypes[$firstSheetAsCollection[$rowIndex][$seatIndex]],
                ];
            }
        }

        TheaterRoomRow::query()->insert($rowData);
        TheaterRoomSeat::query()->insert($seats);
    }
}
