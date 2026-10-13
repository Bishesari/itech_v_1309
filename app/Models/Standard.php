<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Standard extends Model
{
    protected $fillable = [
        'field_id',
        'code',
        'name_fa',
        'name_en',
        'nazari_h',
        'amali_h',
        'karvarzi_h',
        'project_h',
        'sum_h',
        'required_h',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'nazari_h' => 'decimal:1',
            'amali_h' => 'decimal:1',
            'karvarzi_h' => 'decimal:1',
            'project_h' => 'decimal:1',
            'sum_h' => 'decimal:1',
            'required_h' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('number');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
    }
}
