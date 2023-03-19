<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSeatMapSpreadsheetRequest;
use App\UseCases\ImportSeatMapSpreadsheetUseCase;

class SeatMapImportController
{
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
        $filePath = storage_path('app/sample_seat_map.xlsx');
        return response()->download($filePath);
    }
}
