<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
           
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['status' => false, 'errors' => $validator->errors()],422)
        );
    }
}
