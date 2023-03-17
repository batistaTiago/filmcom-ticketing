<?php

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait FailedValidationJsonResponse
{
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
