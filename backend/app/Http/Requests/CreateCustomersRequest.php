<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // '*.' is used to validate each field in an array of objects
            '*.first_name' => 'required|string|max:20',
            '*.last_name' => 'required|string|max:20',
            '*.email' => 'required|email',
            '*.phone' => 'required|string|max:15',
            '*.address' => 'required|string|max:40',
            '*.city' => 'required|string|max:20',
            '*.zip_code' => 'required|string|max:10',
        ];
    }
}
