<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class LoginUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|regex:/^[a-zA-Z0-9.]+@[a-zA-Z0-9-]+(\.[a-zA-Z]+)$/',
            'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$*&])[A-Za-z\d@$*&]{8,}$/',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.regex' => 'Invalid email format.',
            'email.exists' => 'Invalid email.',
            'password.required' => 'The password field is required.',
            'password.regex' => 'The password field must contain at least 1 lowercase letter, 1 uppercase letter, 1 digit, 1 special character (@$*&), and be at least 8 characters long.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['status' => false, 'errors' => $validator->errors()],422)
        );
    }
}
