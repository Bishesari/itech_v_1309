<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductStandard extends Pivot
{
    protected $table = 'product_standard';
    protected $fillable = [
        'product_id',
        'standard_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }
}
