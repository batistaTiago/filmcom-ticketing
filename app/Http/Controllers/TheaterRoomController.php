<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreatedFilmJsonResource;
use App\UseCases\ShowTheaterRoomUseCase;
use Illuminate\Http\Request;

class TheaterRoomController extends Controller
{

    public function __construct(private readonly ShowTheaterRoomUseCase $useCase)
    { }

    public function show(Request $request)
    {
        return response()->json($this->useCase->execute($request->room_id));
    }
}
