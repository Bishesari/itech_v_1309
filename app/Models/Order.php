<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function productAccesses(): HasMany
    {
        return $this->hasManyThrough(
            ProductAccess::class,
            OrderItem::class,
            'order_id',
            'order_item_id',
        );
    }
}
