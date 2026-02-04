<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'order_item',
            'attributes' => [
                'product_id' => $this->product_id,
                'product_name' => $this->product->name ?? null,
                'quantity' => $this->quantity,
                'unit_price' => [
                    'amount' => $this->unit_price,
                    'currency' => 'USD',
                    'formatted' => number_format($this->unit_price, 2) . ' USD',
                ],
                'subtotal' => [
                    'amount' => $this->subtotal,
                    'currency' => 'USD',
                    'formatted' => number_format($this->subtotal, 2) . ' USD',
                ],
            ],
        ];
    }
}
