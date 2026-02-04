<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'order',
            'attributes' => [
                'order_number' => $this->order_number,
                'customer_id' => $this->customer_id,
                'status' => [
                    'value' => $this->status,
                    'label' => $this->getStatusLabel(),
                    'is_cancellable' => $this->is_cancellable,
                ],
                'total_amount' => [
                    'amount' => $this->total_amount,
                    'currency' => 'USD',
                    'formatted' => number_format($this->total_amount, 2) . ' USD',
                ],
                'notes' => $this->notes,
                'items' => OrderItemResource::collection($this->whenLoaded('items')),
                'items_count' => $this->whenLoaded('items', fn() => $this->items->count()),
            ],
            'meta' => [
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }
}
