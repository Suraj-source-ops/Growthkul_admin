<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
            // 'client_id' => 'required',
            'product_type' => 'required',
            'product_code' => 'required|string|max:50',
            'description' => 'required|string',
            'graphic_product_type' => 'required',
            'team_id' => 'required',
            'member_id' => 'required',
            'product_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx',
        ];
    }

    public function messages(): array
    {
        return [
            'product_type.required' => 'Please select a product type',
            'product_code.required' => 'Please enter a product code',
            'product_code.string' => 'The product code must be a string',
            'product_code.max' => 'The product code may not be greater than 50 characters',
            'graphic_product_type.required' => 'Please select a product\'s graphic types',
            'team_id.required' => 'Please select a team',
            'member_id.required' => 'Please select a team member',
            'product_files.*.file' => 'The uploaded file must be a valid file',
            'product_files.*.mimes' => 'The uploaded file must be a file of type: jpg, jpeg, png, pdf, doc, docx',
            'product_files.*.max' => 'The uploaded file may not be greater than 10MB',
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
