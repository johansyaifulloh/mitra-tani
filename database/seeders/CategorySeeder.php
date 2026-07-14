<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pupuk', 'slug' => 'pupuk', 'icon' => '🌱', 'color' => '#059669', 'display_order' => 1],
            ['name' => 'Benih', 'slug' => 'benih', 'icon' => '🌾', 'color' => '#d97706', 'display_order' => 2],
            ['name' => 'Obat Tanaman', 'slug' => 'obat-tanaman', 'icon' => '💊', 'color' => '#dc2626', 'display_order' => 3],
            ['name' => 'Alat Tani', 'slug' => 'alat-tani', 'icon' => '🔧', 'color' => '#2563eb', 'display_order' => 4],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true]),
            );
        }
    }
}
