<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $brand = $this->user()?->brand;

        if (! $brand) {
            return false;
        }

        $product = $this->route('product');

        return $product === null || $product->brand_id === $brand->id;
    }

    public function rules(): array
    {
        $expiryRules = ['nullable', 'date'];
        if ($this->filled('production_date')) {
            $expiryRules[] = 'after_or_equal:production_date';
        }

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'quality_level' => ['required', Rule::in(Product::QUALITY_LEVELS)],
            'grade' => ['nullable', Rule::in(Product::GRADES)],
            'ingredients' => ['required', 'string', 'max:1000'],
            'net_quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'unit' => ['required', Rule::in(Product::UNITS)],
            'production_date' => ['nullable', 'date'],
            'expiry_date' => $expiryRules,
            'batch_code' => ['nullable', 'string', 'max:50'],
            'is_organic' => ['boolean'],
            'growing_method' => ['nullable', Rule::in(Product::GROWING_METHODS)],
            'price' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['boolean'],
        ];
    }
}