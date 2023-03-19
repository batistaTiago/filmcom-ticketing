<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class CreateTheaterRoomRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'theater_id' => ['required', 'exists:theaters,uuid']
        ];
    }
}
