<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFilmRequest;
use App\UseCases\CreateFilmUseCase;
use App\UseCases\ListFilmsUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilmController
{
    public function index(Request $request, ListFilmsUseCase $useCase)
    {
        return response()->json($useCase->execute($request->all()));
    }

    public function store(CreateFilmRequest $request, CreateFilmUseCase $useCase)
    {
        $data = array_merge($request->validated(), ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($useCase->execute($data), 201);
    }
}
