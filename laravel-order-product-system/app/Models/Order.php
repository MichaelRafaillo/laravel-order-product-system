<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => 'string'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Helper methods
    public function calculateTotal(): float
    {
        return $this->items->sum('subtotal');
    }

    public function is cancellable(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
