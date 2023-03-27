<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class RemoveTicketFromCartRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['required', 'string', 'exists:tickets,uuid'],
            'cart_id' => ['required', 'string', 'exists:carts,uuid'],
        ];
    }
}
