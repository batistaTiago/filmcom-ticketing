<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExhibitionRequest;
use App\UseCases\CreateExhibitionUseCase;
use App\UseCases\ListFilmExhibitionsUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExhibitionController
{
    public function index(Request $request, ListFilmExhibitionsUseCase $useCase)
    {
        return response()->json($useCase->execute($request->film_id));
    }

    public function store(CreateExhibitionRequest $request, CreateExhibitionUseCase $useCase)
    {
        $data = array_merge($request->all(), ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($useCase->execute($data), 201);
    }

}
