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
            'order_number' => $this->order_number,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'total_amount' => $this->total_amount,
            'formatted_total' => number_format($this->total_amount, 2),
            'notes' => $this->notes,
            'is_cancellable' => $this->is_cancellable,
            'items_count' => $this->whenLoaded('items', fn() => $this->items->count()),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
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
