<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
            'productId' => 'required',
            'description' => 'required|string',
            'graphic_product_type' => 'required',
            'product_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'Product Id is required',
            'graphic_product_type.required' => 'Please select a product\'s graphic types',
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
