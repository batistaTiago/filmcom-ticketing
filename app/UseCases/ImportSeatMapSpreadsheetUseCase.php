<?php

namespace App\UseCases;

use App\Models\SeatType;
use App\Models\TheaterRoomRow;
use App\Models\TheaterRoomSeat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportSeatMapSpreadsheetUseCase
{
//    public function __construct(
//        private readonly SeatTypeRepositoryInterface $seatTypeRepository,
//        private readonly TheaterRoomRowRepositoryInterface $theaterRoomRowRepository,
//        private readonly TheaterRoomSeatRepositoryInterface $seatRepository,
//    ) {
//    }

    public function execute(
        string $theater_room_id,
        UploadedFile $file,
        bool $shouldRebuildMap
    ) {
        // layerize this code
        $sheetsAsCollection = Excel::toCollection((object) [], $file);
        $firstSheetAsCollection = $sheetsAsCollection->first();

        $seatTypes = $this->getSeatTypeUuids();
        $seatNames = $this->getSeatNamesFromSpreadsheet($firstSheetAsCollection);
        $rowNames = $this->getRowNamesFromSpreadsheet($firstSheetAsCollection);

        try {
            DB::beginTransaction();

            if ($shouldRebuildMap) {
                $this->destroyCurrentMap($theater_room_id);
            }

            $this->buildSeatMap($rowNames, $seatNames, $theater_room_id, $seatTypes, $firstSheetAsCollection);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
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
        foreach ($rowNames as $rowIndex => $rowName) {
            $createdRow = TheaterRoomRow::query()->create([
                'uuid' => Str::orderedUuid()->toString(),
                'name' => $rowName,
                'theater_room_id' => $theater_room_id,
            ]);

            foreach ($seatNames as $seatIndex => $seatName) {
                TheaterRoomSeat::query()->create([
                    'uuid' => Str::orderedUuid()->toString(),
                    'name' => $seatName,
                    'theater_room_row_id' => $createdRow->uuid,
                    'seat_type_id' => $seatTypes[$firstSheetAsCollection[$rowIndex][$seatIndex]],
                ]);
            }
        }
    }
}

