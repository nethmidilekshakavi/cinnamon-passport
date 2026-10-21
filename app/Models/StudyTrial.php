<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyTrial extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'trial_date' => 'date',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'success' => 'boolean',
        ];
    }
}
