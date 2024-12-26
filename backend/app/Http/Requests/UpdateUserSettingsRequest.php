<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingsRequest extends FormRequest
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
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'bio' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:10',
        ];
    }
}
