<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTheaterRequest;
use App\Http\Resources\CreatedFilmJsonResource;
use App\UseCases\CreateTheaterUseCase;
use Illuminate\Support\Str;

class TheaterController extends Controller
{

    public function __construct(private readonly CreateTheaterUseCase $useCase)
    { }

    public function store(CreateTheaterRequest $request)
    {
        $data = array_merge($request->validated(), ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($this->useCase->execute($data));
    }
}
