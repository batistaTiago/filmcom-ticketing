<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\FailedValidationJsonResponse;
use App\Http\Rules\DayOfWeekRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExhibitionRequest extends FormRequest
{
    use FailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'starts_at' => ['sometimes', 'date_format:H:i,H:i:s'],
            'day_of_week' => ['sometimes', new DayOfWeekRule()],
            'is_active' => ['sometimes', 'boolean']
        ];
    }
}
