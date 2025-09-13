<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            // 'description' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The client name is required.',
            'name.string' => 'The client name must be a string.',
            'name.max' => 'The client name may not be greater than 255 characters.',
            'email.required' => 'Please enter an email address',
            'email.email' => 'The email must be a valid email address',
            'phone.required' => 'Please enter a phone number',
            'phone.numeric' => 'The phone number must be a valid number',
            // 'description.required' => 'The description is required.',
            // 'description.string' => 'The description must be a string.',
            // 'description.max' => 'The description may not be greater than 500 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
        throw new HttpResponseException(
            redirect()->back()
                ->withInput()
                ->with('message', $firstError)
                ->with('alert-type', 'error')
        );
    }
}
