<?php

namespace App\Http\Controllers;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\CreateExhibitionRequest;
use App\Http\Requests\UpdateExhibitionRequest;
use App\Models\Exhibition;
use App\UseCases\CreateExhibitionUseCase;
use App\UseCases\ListExhibitionTicketTypesUseCase;
use App\UseCases\ListFilmExhibitionsUseCase;
use App\UseCases\UpdateExhibitionUseCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ExhibitionController
{
    public function index(Request $request, ListFilmExhibitionsUseCase $useCase)
    {
        return response()->json($useCase->execute($request->film_id));
    }

    public function store(CreateExhibitionRequest $request, CreateExhibitionUseCase $useCase)
    {
        $data = array_merge($request->validated()['exhibition'], ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($useCase->execute($data, $request->ticket_type_ids ?? []), 201);
    }

    public function getTicketTypes(Request $request, ListExhibitionTicketTypesUseCase $useCase)
    {
        return response()->json($useCase->execute($request->exhibition_id));
    }

    public function update(UpdateExhibitionRequest $request, UpdateExhibitionUseCase $useCase)
    {
        $useCase->execute($request->exhibition_id, $request->validated());
        return response()->json(['success' => true]);
    }
}
