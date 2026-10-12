<?php

namespace App\Http\Requests;

use App\Models\Batch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('grower') ?? false;
    }

    public function rules(): array
    {
        return [
            'plot' => ['required', 'string', 'max:30'],
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'harvest_date' => ['required', 'date', 'before_or_equal:today'],
            'drying_days' => ['required', 'integer', 'min:1', 'max:30'],
            'grade' => ['nullable', Rule::in(Batch::GRADES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
