<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierRequest extends FormRequest
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
            'name' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:40',
            'city' => 'required|string|max:20',
            'zip_code' => 'required|string|max:10',
            'website' => 'nullable|string|max:100',
        ];
    }
}
