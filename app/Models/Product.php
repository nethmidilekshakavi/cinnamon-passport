<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasUniqueSlug;

    public const QUALITY_LEVELS = ['high', 'medium', 'low'];
    public const GRADES = ['Alba', 'C5', 'C4', 'H1'];
    public const GROWING_METHODS = ['organic', 'compost', 'inorganic'];
    public const UNITS = ['g', 'ml'];

    protected $fillable = [
        'brand_id', 'category_id', 'name', 'slug', 'description',
        'quality_level', 'grade', 'ingredients', 'net_quantity', 'unit',
        'production_date', 'expiry_date', 'batch_code', 'is_organic',
        'growing_method', 'price', 'stock', 'image_path', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'expiry_date' => 'date',
            'price' => 'decimal:2',
            'is_organic' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }
}