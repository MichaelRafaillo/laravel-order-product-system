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
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? null,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'formatted_unit_price' => number_format($this->unit_price, 2),
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => number_format($this->subtotal, 2),
        ];
    }
}
