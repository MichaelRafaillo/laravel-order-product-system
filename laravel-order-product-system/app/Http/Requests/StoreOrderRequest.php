<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:users,id',
            'status' => 'sometimes|in:pending,processing,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required',
            'items.required' => 'Order must have at least one item',
            'items.*.product_id.exists' => 'One or more products do not exist',
            'items.*.quantity.min' => 'Quantity must be at least 1',
        ];
    }
}
