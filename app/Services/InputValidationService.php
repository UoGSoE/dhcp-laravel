<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class InputValidationService
{
    public static function validateInput(
        array $data,
        array $rules,
        array $messages = [],
        array $validationErrors,
    ): array {
        $validation = Validator::make(
            $data,
            $rules,
            $messages
        );


        foreach ($data as $key => $value) {
            $error = $validation->errors()->get($key);

            if ($error) {
                $validationErrors[$key] = $error;
            } else {
                unset($validationErrors[$key]);
            }
        }

        // Validation error for this field
        // $error = $validation->errors()->get($inputField);

        // // Save validation errors for this field
        // if ($error) {
        //     $validationErrors[$inputField] = $error;
        // } else {
        //     unset($validationErrors[$inputField]);
        // }

        return $validationErrors;
    }
}
