<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class ImportSeatMapSpreadsheetRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'theater_room_id' => ['required', 'exists:theater_rooms,uuid'],
            'file' => ['required', 'mimes:xlsx,xls,csv', 'max:4096'],
            'should_rebuild_map' => ['sometimes', 'boolean']
        ];
    }
}
