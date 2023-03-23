<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSeatMapSpreadsheetRequest;
use App\UseCases\ImportSeatMapSpreadsheetUseCase;
use Illuminate\Filesystem\FilesystemManager;

class SeatMapImportController
{
    public function __construct(private readonly FilesystemManager $fileSystem)
    { }

    public function importSeatMapSpreadsheet(ImportSeatMapSpreadsheetRequest $request, ImportSeatMapSpreadsheetUseCase $useCase)
    {
        $useCase->execute(
            $request->theater_room_id,
            $request->file('file'),
            $request->should_rebuild_map ?? false
        );
        return response()->json(['success' => true]);
    }

    public function getSeatMapExampleSpreadsheet()
    {
        return response()->download(
            $this->fileSystem
                ->disk(config('filesystems.default'))
                ->path('app/sample_seat_map.xlsx')
        );
    }
}
