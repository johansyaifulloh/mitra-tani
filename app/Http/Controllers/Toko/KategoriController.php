<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;

class KategoriController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private ProductService $productService,
    ) {}

    public function index()
    {
        $categories = $this->categoryService->listForStore();

        return view('mobile.kategori.index', [
            'categories' => $categories,
            'products' => $this->productService->listActive(),
        ]);
    }
}
