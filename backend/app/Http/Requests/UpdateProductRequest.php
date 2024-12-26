<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'description' => 'nullable|string|max:200',
            'price' => 'required|numeric|max:99999',
            'max_quantity' => 'required|integer|max:500',
            'category_id' => 'nullable|string|exists:categories,id',
            'warehouse_id' => 'nullable|string|exists:warehouses,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ];
    }
}
