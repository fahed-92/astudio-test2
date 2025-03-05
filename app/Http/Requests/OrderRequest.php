<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Order Request
 * 
 * Handles validation for order creation and updates.
 * Ensures all required fields are present and properly formatted.
 *
 * @author Fahed
 * @package App\Http\Requests
 */
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @author Fahed
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @author Fahed
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:1000',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999.99',
            'items.*.quantity' => 'required|integer|min:1|max:9999'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @author Fahed
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'notes.max' => 'Order notes cannot exceed 1000 characters.',
            'items.required' => 'At least one item is required for the order.',
            'items.min' => 'The order must contain at least one item.',
            'items.*.product_name.required' => 'Product name is required for each item.',
            'items.*.product_name.max' => 'Product name cannot exceed 255 characters.',
            'items.*.description.max' => 'Item description cannot exceed 1000 characters.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.unit_price.numeric' => 'Unit price must be a valid number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.unit_price.max' => 'Unit price cannot exceed 999,999.99.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a whole number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.quantity.max' => 'Quantity cannot exceed 9999.'
        ];
    }
} 