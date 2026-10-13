<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'short_description',
        'description',
        'seo_title',
        'seo_description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function standards(): BelongsToMany
    {
        return $this->belongsToMany(Standard::class)
            ->withTimestamps();
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(ProductAccess::class);
    }
}
