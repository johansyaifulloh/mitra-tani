<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Support\FormatHelper;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {}

    public function listForStore(): array
    {
        // STEP 1: Get DB reference
        $rows = $this->categoryRepository->allActive();

        // STEP 2: Validate — empty is ok for store
        return $rows->map(fn ($row) => FormatHelper::categoryForView($row))->all();
    }

    public function listForAdmin(): array
    {
        $rows = $this->categoryRepository->all();

        return $rows->map(fn ($row) => FormatHelper::categoryForView($row))->all();
    }

    public function create(array $data): int
    {
        // STEP 1: Get DB reference
        $slug = Str::slug($data['name']);

        // STEP 2: Validate uniqueness
        if ($this->categoryRepository->findBySlug($slug)) {
            throw new Exception('Kategori dengan nama serupa sudah ada.');
        }

        // STEP 3: Business logic + STEP 4: Insert
        return $this->categoryRepository->insert([
            'name' => $data['name'],
            'slug' => $slug,
            'icon' => $data['icon'] ?? '📦',
            'color' => $data['color'] ?? '#059669',
            'description' => $data['description'] ?? null,
            'display_order' => $data['display_order'] ?? 0,
            'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true,
        ]);
    }

    public function update(int $id, array $data): void
    {
        // STEP 1: Get DB reference
        $category = $this->categoryRepository->findById($id);

        // STEP 2: Validate DB results
        if (! $category) {
            throw new Exception('Kategori tidak ditemukan.');
        }

        $slug = isset($data['name']) ? Str::slug($data['name']) : $category->slug;
        $existing = $this->categoryRepository->findBySlug($slug);

        if ($existing && $existing->id !== $id) {
            throw new Exception('Kategori dengan nama serupa sudah ada.');
        }

        // STEP 4: Update
        $this->categoryRepository->update($id, array_filter([
            'name' => $data['name'] ?? null,
            'slug' => isset($data['name']) ? $slug : null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'description' => $data['description'] ?? null,
            'display_order' => $data['display_order'] ?? null,
            'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : null,
        ], fn ($v) => $v !== null));
    }

    public function paginateForAdmin(
        int $page,
        int $perPage,
        string $search = '',
        array $categoryIds = [],
        array $statuses = [],
    ): LengthAwarePaginator {
        return $this->categoryRepository->paginateForAdmin($page, $perPage, $search, $categoryIds, $statuses);
    }

    public function toggleActive(int $id): array
    {
        $category = $this->categoryRepository->findById($id);

        if (! $category) {
            throw new Exception('Kategori tidak ditemukan.');
        }

        $isActive = ! (bool) $category->is_active;

        $this->categoryRepository->update($id, [
            'is_active' => $isActive,
        ]);

        return [
            'is_active' => $isActive,
            'status_label' => $isActive ? 'Aktif' : 'Nonaktif',
            'message' => $isActive
                ? 'Kategori diaktifkan dan tampil di toko.'
                : 'Kategori dinonaktifkan. Produk tetap ada, tapi kategori tidak tampil di toko.',
        ];
    }

    public function delete(int $id): void
    {
        $category = $this->categoryRepository->findById($id);

        if (! $category) {
            throw new Exception('Kategori tidak ditemukan.');
        }

        if ($this->categoryRepository->countProducts($id) > 0) {
            throw new Exception('Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        $this->categoryRepository->delete($id);
    }
}
