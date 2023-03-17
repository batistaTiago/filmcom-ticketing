<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class CreateTheaterRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
