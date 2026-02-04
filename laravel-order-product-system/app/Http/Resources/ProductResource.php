<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'product',
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'price' => [
                    'amount' => $this->price,
                    'currency' => 'USD',
                    'formatted' => number_format($this->price, 2) . ' USD',
                ],
                'stock_quantity' => $this->stock_quantity,
                'sku' => $this->sku,
                'is_active' => $this->is_active,
                'status' => $this->is_active ? 'active' : 'inactive',
            ],
            'meta' => [
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
