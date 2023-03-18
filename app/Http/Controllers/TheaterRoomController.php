<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSeatMapSpreadsheetRequest;
use App\Http\Resources\CreatedFilmJsonResource;
use App\UseCases\ImportSeatMapSpreadsheetUseCase;
use App\UseCases\ShowTheaterRoomAvailabilityUseCase;
use App\UseCases\ShowTheaterRoomUseCase;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TheaterRoomController extends Controller
{

    public function show(Request $request, ShowTheaterRoomUseCase $useCase)
    {
        return response()->json($useCase->execute($request->room_id));
    }

    public function showAvailability(Request $request, ShowTheaterRoomAvailabilityUseCase $useCase)
    {
        return response()->json($useCase->execute([
            'room_id' => $request->room_id,
            'exhibition_id' => $request->exhibition_id
        ]));
    }

    public function importSeatMapSpreadsheet(ImportSeatMapSpreadsheetRequest $request, ImportSeatMapSpreadsheetUseCase $useCase)
    {
        $useCase->execute($request->theater_room_id, $request->file('file'), $request->should_rebuild_map ?? false);
        return response()->json(['success' => true]);
    }
}
