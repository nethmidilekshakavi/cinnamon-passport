<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySus extends Model
{
    protected $table = 'study_sus';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'float',
        ];
    }
}
