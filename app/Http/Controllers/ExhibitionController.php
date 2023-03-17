<?php

namespace App\Http\Controllers;

use App\Domain\DTO\ExhibitionDTO;
use App\Domain\Services\RoomAvailabilityServiceInterface;
use App\Http\Requests\CreateExhibitionRequest;
use App\Http\Requests\UpdateExhibitionRequest;
use App\Models\Exhibition;
use App\UseCases\CreateExhibitionUseCase;
use App\UseCases\ListExhibitionTicketTypesUseCase;
use App\UseCases\ListFilmExhibitionsUseCase;
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
        $data = array_merge($request->validated(), ['uuid' => Str::orderedUuid()->toString()]);
        return response()->json($useCase->execute($data), 201);
    }

    public function getTicketTypes(Request $request, ListExhibitionTicketTypesUseCase $useCase)
    {
        return response()->json($useCase->execute($request->exhibition_id));
    }

    public function update(UpdateExhibitionRequest $request)
    {
        // TODO layerize this code
        $exhibitionDTO = Exhibition::where('uuid', $request->exhibition_id)->first();

        $updatedDtoData = array_merge(
            Arr::only($exhibitionDTO->toArray(), ExhibitionDTO::ATTRIBUTES),
            $request->validated(),
            ['starts_at' => Carbon::parse($request->starts_at)->toTimeString()]
        );

        resolve(RoomAvailabilityServiceInterface::class)->validate(ExhibitionDTO::fromArray($updatedDtoData));
        Exhibition::where('uuid', $request->exhibition_id)->update($updatedDtoData);
        return response()->json(['success' => true]);
    }
}
