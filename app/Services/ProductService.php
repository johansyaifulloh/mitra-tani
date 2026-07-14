<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Support\FormatHelper;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function listActive(?int $categoryId = null): array
    {
        $rows = $this->productRepository->listActive($categoryId);

        return $rows->map(fn ($row) => FormatHelper::productForMobile($row))->all();
    }

    public function fetchActivePaginated(int $page, int $perPage, string $search = '', array $categoryIds = []): array
    {
        $paginator = $this->productRepository->paginateActive($perPage, $page, $search, $categoryIds);

        return [
            'success' => true,
            'data' => collect($paginator->items())
                ->map(fn ($row) => FormatHelper::productForMobile($row))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    public function findBySlug(string $slug): ?array
    {
        $row = $this->productRepository->findBySlug($slug);

        if (! $row || $row->status !== 'active') {
            return null;
        }

        return FormatHelper::productForMobile($row);
    }

    public function paginateForAdminView(int $perPage = 8): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage)->through(function ($row) {
            return [
                'id' => $row->id,
                'emoji' => $row->emoji ?? '🌱',
                'name' => $row->name,
                'cat' => $row->category_name,
                'price' => FormatHelper::rupiah($row->price),
                'stock' => $row->stock,
                'status' => $row->status,
            ];
        });
    }

    public function create(array $data, ?UploadedFile $image = null): int
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data kategori dari database
        // Digunakan untuk validasi relasi produk-kategori
        //--------------------------------------------------
        $category = $this->categoryRepository->findById((int) $data['category_id']);

        //--------------------------------------------------
        // STEP 2
        // Memastikan kategori tersedia sebelum simpan produk
        //--------------------------------------------------
        if (! $category) {
            throw new Exception('Kategori tidak ditemukan.');
        }

        //--------------------------------------------------
        // STEP 3
        // Menyiapkan slug dan status produk
        //--------------------------------------------------
        $slug = $this->resolveUniqueSlug($data['name']);
        $status = ($data['status'] ?? 'active') === 'draft' ? 'draft' : 'active';
        $imagePath = $image ? $image->store('products', 'public') : null;

        //--------------------------------------------------
        // STEP 4
        // Menyimpan data produk ke database
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            $productId = $this->productRepository->insert([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'emoji' => $data['emoji'] ?? '🌱',
                'image_path' => $imagePath,
                'badge' => $data['badge'] ?: null,
                'status' => $status,
            ]);

            DB::commit();

            return $productId;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductService::create failed', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(int $id, array $data, ?UploadedFile $image = null): void
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data produk dari database
        // Digunakan untuk validasi sebelum update
        //--------------------------------------------------
        $product = $this->productRepository->findById($id);

        //--------------------------------------------------
        // STEP 2
        // Memastikan produk tersedia
        //--------------------------------------------------
        if (! $product) {
            throw new Exception('Produk tidak ditemukan.');
        }

        if (isset($data['category_id'])) {
            $category = $this->categoryRepository->findById((int) $data['category_id']);

            if (! $category) {
                throw new Exception('Kategori tidak ditemukan.');
            }
        }

        //--------------------------------------------------
        // STEP 3
        // Menyiapkan data yang akan diperbarui
        //--------------------------------------------------
        $update = array_filter([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'stock' => $data['stock'] ?? null,
            'emoji' => $data['emoji'] ?? null,
            'image_path' => $image ? $image->store('products', 'public') : null,
            'badge' => array_key_exists('badge', $data) ? ($data['badge'] ?: null) : null,
            'status' => isset($data['status']) ? ($data['status'] === 'draft' ? 'draft' : 'active') : null,
        ], fn ($v) => $v !== null);

        if (isset($data['name'])) {
            $update['slug'] = Str::slug($data['name']);
        }

        //--------------------------------------------------
        // STEP 4
        // Menyimpan perubahan produk ke database
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            $this->productRepository->update($id, $update);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductService::update failed', ['message' => $e->getMessage(), 'product_id' => $id]);
            throw $e;
        }
    }

    public function destroy(int $id): void
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data produk dari database
        //--------------------------------------------------
        $product = $this->productRepository->findById($id);

        //--------------------------------------------------
        // STEP 2
        // Memastikan produk tersedia sebelum diarsipkan
        //--------------------------------------------------
        if (! $product) {
            throw new Exception('Produk tidak ditemukan.');
        }

        //--------------------------------------------------
        // STEP 3
        // Mengubah status produk menjadi draft (soft delete)
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            $this->productRepository->softDelete($id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductService::destroy failed', ['message' => $e->getMessage(), 'product_id' => $id]);
            throw $e;
        }
    }

    private function resolveUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        if ($this->productRepository->findBySlug($slug)) {
            $slug = $slug.'-'.Str::random(4);
        }

        return $slug;
    }
}
