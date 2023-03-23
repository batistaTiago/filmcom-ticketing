<?php

namespace App\Http\Controllers;

use App\Domain\DTO\TheaterDTO;
use App\Http\Requests\CreateTheaterRequest;
use App\Http\Resources\CreatedFilmJsonResource;
use App\Models\Theater;
use App\UseCases\CreateTheaterUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TheaterController
{

    public function __construct(private readonly CreateTheaterUseCase $useCase)
    { }

    public function index(Request $request)
    {
        // TODO layerize this code
        $paginatedResult = Theater::query()->paginate(10)->appends(request()->query());
        $paginatedResult->getCollection()->transform(fn ($theater) => $theater->toDto());
        return response()->json($paginatedResult);
    }

    public function store(CreateTheaterRequest $request)
    {
        $data = array_merge($request->validated(), ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($this->useCase->execute($data));
    }

}
