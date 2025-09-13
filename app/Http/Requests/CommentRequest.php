<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
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
            'product_type' => 'required',
            'comment' => 'required|string|max:2000',
            // 'comment_by' => 'required',
            'comment_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'Product id is required',
            'product_type.required' => 'Please select a product type',
            'comment.required' => 'Please enter a comment',
            'comment.string' => 'The comment must be a string',
            'comment.max' => 'The comment may not be greater than 2000 characters',
            'comment_by.required' => 'commented by user is missing',
            'comment_files.*.file' => 'The uploaded file must be a valid file',
            'comment_files.*.mimes' => 'The uploaded file must be a file of type: jpg, jpeg, png, pdf, doc, docx',
            'comment_files.*.max' => 'The uploaded file may not be greater than 2MB',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
        throw new HttpResponseException(
            response()->json(['status' => false, 'message' => $firstError, 'alert-type' => 'error'])
        );
    }
}
