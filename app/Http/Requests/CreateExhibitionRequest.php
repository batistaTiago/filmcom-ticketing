<?php

namespace App\Http\Requests;

use App\Http\Rules\DayOfWeekRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'film_id' => ['required', 'string', 'exists:films,uuid'],
            'theater_room_id' => ['required', 'string', 'exists:theater_rooms,uuid'],
            'starts_at' => ['required', 'date_format:H:i,H:i:s'],
            'day_of_week' => ['required', new DayOfWeekRule()],
            'is_active' => ['required', 'boolean']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'The submitted data is invalid',
            'errors' => $errors->messages(),
        ], 400);

        throw new HttpResponseException($response);
    }
}
