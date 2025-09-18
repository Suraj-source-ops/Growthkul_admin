<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TeamMemberRequest extends FormRequest
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
            'team' => 'required',
            'member_name' => 'required|string|max:255',
            'role' => 'required',
            'mobile' => 'required|numeric',
            'email' => 'required|email|unique:users,email',
            'profile_pic' => 'nullable|file|mimes:jpg,jpeg,png|max:200'
        ];
    }

    public function messages(): array
    {
        return [
            'member_name.required' => 'Please enter a name',
            'mobile.required' => 'Please enter a mobile number',
            'mobile.numeric' => 'The mobile number must be a valid number',
            'email.required' => 'Please enter an email address',
            'email.email' => 'The email must be a valid email address',
            'role.required' => 'Please select a role',
            'team.required' => 'Please select a team',
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
