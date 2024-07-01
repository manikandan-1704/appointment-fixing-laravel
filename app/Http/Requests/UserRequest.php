<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        $userId = $this->id;
        return [
            'name' => 'required|regex:/^[a-zA-Z .]+$/|min:3|max:100',
            'email' => ['required','regex:/^[a-zA-Z0-9.]+@[a-zA-Z0-9-]+(\.[a-zA-Z]+)$/', 'max:255', Rule::unique('users')->whereNull('deleted_at')->ignore($userId)],
            'password' => ['required', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@_-])[A-Za-z\d@_-]{8,}$/'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The first name field is required.',
            'name.regex' => 'The first name field contains invalid characters.',
            'name.min' => 'The first name field must be at least 3 characters.',
            'name.max' => 'The first name field cannot be more than 100 characters.',
            'email.required' => 'The email field is required.',
            'email.regex' => 'Invalid email format.',
            'email.unique' => 'The email has already been taken.',
            'email.max' => 'The email field cannot be more than 255 characters.',
            'password.required' => 'The password field is required.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['status' => false, 'errors' => $validator->errors()],422)
        );
    }
}
