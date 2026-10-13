<?php

namespace App\Models;

use App\Enums\ProductType;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
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
            'type' => ProductType::class,
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

    public function isStandardQuestionBank(): bool
    {
        return $this->type === ProductType::StandardQuestionBank;
    }

    public function currentPrice(?CarbonInterface $at = null): ?ProductPrice
    {
        $at ??= now();

        /** @var Builder<ProductPrice> $query */
        $query = $this->prices();

        return $query
            ->where('starts_at', '<=', $at)
            ->where(function (Builder $query) use ($at) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', $at);
            })
            ->orderByDesc('starts_at')
            ->first();
    }
}
