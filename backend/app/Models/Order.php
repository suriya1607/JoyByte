<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // Status ID constants
    public const STATUS_ID_PENDING = 1;
    public const STATUS_ID_CONFIRMED = 2;
    public const STATUS_ID_PREPARING = 3;
    public const STATUS_ID_OUT_FOR_DELIVERY = 4;
    public const STATUS_ID_DELIVERED = 5;
    public const STATUS_ID_CANCELLED = 6;

    // Payment Method ID constants
    public const PAYMENT_METHOD_ID_COD = 1;
    public const PAYMENT_METHOD_ID_ONLINE = 2;
    public const PAYMENT_METHOD_ID_CARD = 3;

    // String constants (for API responses if needed)
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_COD = 'cod';
    public const PAYMENT_METHOD_ONLINE = 'online';
    public const PAYMENT_METHOD_CARD = 'card';

    protected $fillable = [
        'user_id',
        'shop_id',
        'address_id',
        'status',
        'payment_method',
        'subtotal',
        'delivery_fee',
        'total',
        'delivery_address',
        'notes',
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'subtotal'         => 'decimal:2',
        'delivery_fee'     => 'decimal:2',
        'total'            => 'decimal:2',
        'status'           => 'integer',
        'payment_method'   => 'integer',
    ];

    protected $appends = [
        'status_label',
        'status_value',
        'payment_method_label',
        'payment_method_value',
        'total_amount',
        'items_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get status label from config
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = config('order.status_labels', []);
        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get status value (string) from config
     */
    public function getStatusValueAttribute(): string
    {
        $statuses = config('order.statuses', []);
        return $statuses[$this->status] ?? 'unknown';
    }

    /**
     * Get payment method label from config
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = config('order.payment_method_labels', []);
        return $labels[$this->payment_method] ?? 'Unknown';
    }

    /**
     * Get payment method value (string) from config
     */
    public function getPaymentMethodValueAttribute(): string
    {
        $methods = config('order.payment_methods', []);
        return $methods[$this->payment_method] ?? 'unknown';
    }

    /**
     * Get total amount (alias for total field for API consistency)
     */
    public function getTotalAmountAttribute()
    {
        return $this->total;
    }

    /**
     * Get count of items in this order
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get all available status IDs
     */
    public static function getAvailableStatusIds(): array
    {
        return array_keys(config('order.statuses', []));
    }

    /**
     * Get all available payment method IDs
     */
    public static function getAvailablePaymentMethodIds(): array
    {
        return array_keys(config('order.payment_methods', []));
    }

    /**
     * Get status ID from string value
     */
    public static function getStatusId(string $statusValue): ?int
    {
        return config('order.status_ids.' . $statusValue);
    }

    /**
     * Get payment method ID from string value
     */
    public static function getPaymentMethodId(string $methodValue): ?int
    {
        return config('order.payment_method_ids.' . $methodValue);
    }
}
