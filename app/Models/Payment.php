<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_PAID = 'paid';
    public const string STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'status',
        'gateway',
        'res_num',
        'gateway_token',
        'gateway_reference',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
