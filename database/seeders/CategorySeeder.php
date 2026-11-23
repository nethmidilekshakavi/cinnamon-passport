<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Cinnamon sticks',
            'Cinnamon powder',
            'Cinnamon tea',
            'Cinnamon oil',
            'Cinnamon balm',
            'Cinnamon perfume',
        ];

        foreach ($names as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}