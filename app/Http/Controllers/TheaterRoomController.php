<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTheaterRoomRequest;
use App\UseCases\CreateTheaterRoomUseCase;
use App\UseCases\ShowTheaterRoomAvailabilityUseCase;
use App\UseCases\ShowTheaterRoomUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TheaterRoomController
{
    public function store(CreateTheaterRoomRequest $request, CreateTheaterRoomUseCase $useCase)
    {
        $data = array_merge($request->validated(), ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($useCase->execute($data), 201);
    }

    public function show(Request $request, ShowTheaterRoomUseCase $useCase)
    {
        return response()->json($useCase->execute($request->room_id));
    }

    public function showAvailability(Request $request, ShowTheaterRoomAvailabilityUseCase $useCase)
    {
        return response()->json($useCase->execute([
            'exhibition_id' => $request->exhibition_id
        ]));
    }
}
