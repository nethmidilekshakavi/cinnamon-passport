<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    use HasFactory;

    public const SOURCE_APP = 'app';
    public const SOURCE_SMS = 'sms';

    public const STATUS_RECORDED = 'recorded';
    public const STATUS_FLAGGED = 'flagged';
    public const STATUS_HANDED_OVER = 'handed_over';

    protected $fillable = [
        'grower_id',
        'plot',
        'weight_kg',
        'harvest_date',
        'drying_days',
        'grade',
        'source',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'weight_kg' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // Give every new batch a readable code such as CIN-0042.
        static::created(function (Batch $batch) {
            $batch->forceFill(['code' => sprintf('CIN-%04d', $batch->id)])->saveQuietly();
        });
    }

    public function grower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grower_id');
    }
}
