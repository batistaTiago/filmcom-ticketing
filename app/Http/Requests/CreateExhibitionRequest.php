<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use App\Http\Rules\DayOfWeekRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateExhibitionRequest extends FormRequest
{
    use FailedValidationJsonResponse;

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
}
