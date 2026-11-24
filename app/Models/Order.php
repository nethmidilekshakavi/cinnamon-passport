<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const PLACED = 'placed';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';

    // Never filled straight from request input: the controllers build these arrays themselves.
    protected $fillable = [
        'order_number', 'user_id', 'brand_id', 'product_id',
        'product_name', 'product_size', 'unit_price', 'quantity', 'total',
        'shipping_name', 'shipping_address', 'shipping_city',
        'shipping_postal_code', 'shipping_country',
        'status', 'payment_method',
        'placed_at', 'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === self::PLACED;
    }

    public function canBeShipped(): bool
    {
        return $this->status === self::PLACED;
    }

    public function canBeMarkedReceived(): bool
    {
        return $this->status === self::SHIPPED;
    }
}