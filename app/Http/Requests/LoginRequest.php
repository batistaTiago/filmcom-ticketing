<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use App\Http\Rules\DayOfWeekRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_id' => ['sometimes', 'string', 'min:2']
        ];
    }
}
