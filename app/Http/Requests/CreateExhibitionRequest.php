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
            'exhibition' => ['required'],
            'exhibition.film_id' => ['required', 'string', 'exists:films,uuid'],
            'exhibition.theater_room_id' => ['required', 'string', 'exists:theater_rooms,uuid'],
            'exhibition.starts_at' => ['required', 'date_format:H:i,H:i:s'],
            'exhibition.day_of_week' => ['required', new DayOfWeekRule()],
            'exhibition.is_active' => ['required', 'boolean'],
            'ticket_types' => ['sometimes', 'array'],
            'ticket_types.*.uuid' => ['required', 'string', 'exists:ticket_types,uuid'],
            'ticket_types.*.price' => ['required', 'integer', 'min:0'],
        ];
    }
}
