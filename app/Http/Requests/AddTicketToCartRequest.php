<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use App\Http\Rules\DayOfWeekRule;
use Illuminate\Foundation\Http\FormRequest;

class AddTicketToCartRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exhibition_id' => ['required', 'exists:exhibitions,uuid'],
            'ticket_type_id' => ['required', 'exists:ticket_types,uuid'],
            'theater_room_seat_id' => ['required', 'exists:theater_room_seats,uuid'],
            'cart_id' => ['sometimes', 'exists:carts,uuid'],
        ];
    }
}
