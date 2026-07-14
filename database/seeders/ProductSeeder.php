<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = [
            'Pupuk' => 'pupuk',
            'Benih' => 'benih',
            'Obat' => 'obat-tanaman',
            'Alat Tani' => 'alat-tani',
        ];

        $products = [
            ['name' => 'Pupuk Urea 50kg', 'cat' => 'Pupuk', 'price' => 85000, 'badge' => 'Baru', 'emoji' => '🌱', 'slug' => 'pupuk-urea-50kg', 'stock' => 120],
            ['name' => 'Benih Cabai F1', 'cat' => 'Benih', 'price' => 25000, 'badge' => null, 'emoji' => '🌾', 'slug' => 'benih-cabai-f1', 'stock' => 45],
            ['name' => 'Obat Hama Matador', 'cat' => 'Obat', 'price' => 35000, 'badge' => null, 'emoji' => '💊', 'slug' => 'obat-hama-matador', 'stock' => 30],
            ['name' => 'Pupuk NPK 15-15-15', 'cat' => 'Pupuk', 'price' => 95000, 'badge' => 'Promo', 'emoji' => '🌱', 'slug' => 'pupuk-npk', 'stock' => 80],
            ['name' => 'Sprayer 16 Liter', 'cat' => 'Alat Tani', 'price' => 150000, 'badge' => null, 'emoji' => '💧', 'slug' => 'sprayer-16l', 'stock' => 15],
            ['name' => 'Pupuk Organik 5kg', 'cat' => 'Pupuk', 'price' => 45000, 'badge' => 'Terlaris', 'emoji' => '🌿', 'slug' => 'pupuk-organik-5kg', 'stock' => 60],
            ['name' => 'Benih Tomat Hybrid', 'cat' => 'Benih', 'price' => 18000, 'badge' => null, 'emoji' => '🍅', 'slug' => 'benih-tomat', 'stock' => 55],
            ['name' => 'Pupuk Za 50kg', 'cat' => 'Pupuk', 'price' => 78000, 'badge' => null, 'emoji' => '🌱', 'slug' => 'pupuk-za', 'stock' => 90],
            ['name' => 'Fungisida Mancozeb', 'cat' => 'Obat', 'price' => 42000, 'badge' => null, 'emoji' => '💊', 'slug' => 'fungisida-mancozeb', 'stock' => 25],
            ['name' => 'Cangkul Stainless', 'cat' => 'Alat Tani', 'price' => 65000, 'badge' => null, 'emoji' => '🔧', 'slug' => 'cangkul-stainless', 'stock' => 20],
            ['name' => 'Benih Padi IR64', 'cat' => 'Benih', 'price' => 32000, 'badge' => 'Baru', 'emoji' => '🌾', 'slug' => 'benih-padi-ir64', 'stock' => 70],
            ['name' => 'Pupuk KCL 50kg', 'cat' => 'Pupuk', 'price' => 88000, 'badge' => null, 'emoji' => '🌱', 'slug' => 'pupuk-kcl', 'stock' => 40],
        ];

        $categories = Category::query()->pluck('id', 'slug');

        foreach ($products as $product) {
            $categorySlug = $categoryMap[$product['cat']];

            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $categories[$categorySlug],
                    'name' => $product['name'],
                    'description' => 'Produk pertanian berkualitas dari Mantri Tani Selorejo.',
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'emoji' => $product['emoji'],
                    'badge' => $product['badge'],
                    'status' => 'active',
                ],
            );
        }
    }
}
