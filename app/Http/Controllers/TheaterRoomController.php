<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreatedFilmJsonResource;
use App\UseCases\ShowTheaterRoomAvailabilityUseCase;
use App\UseCases\ShowTheaterRoomUseCase;
use Illuminate\Http\Request;

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
}
