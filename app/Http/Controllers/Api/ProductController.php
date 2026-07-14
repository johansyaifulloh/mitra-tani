<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->productService->listActive());
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->findBySlug($slug);

        if (! $product) {
            return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
        }

        return response()->json($product);
    }
}
