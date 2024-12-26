<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'category_id' => 'required|string|exists:categories,id',
            'warehouse_id' => 'required|string|exists:warehouses,id',
            'supplier_id' => 'required|string|exists:suppliers,id',
            'vat_rate' => 'required|string',
            'price' => 'required|numeric|max:99999',
            'quantity' => 'required|integer|lte:max_quantity',
            'max_quantity' => 'required|integer',
        ];
    }
}
