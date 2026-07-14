<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Services\CategoryService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function index()
    {
        // Halaman shell — data kategori dimuat via AJAX (server-side pagination)
        $filterCategories = DB::table('categories')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'icon']);

        return view('admin.kategori.index', compact('filterCategories'));
    }

    public function fetchData(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil parameter dari request (page, search, per_page, filter)
        //--------------------------------------------------
        $page = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('per_page', 50), 1), 100);
        $search = trim($request->query('search', ''));
        $categoryIds = collect($request->query('categories', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
        $statuses = collect($request->query('status', []))
            ->filter(fn ($status) => in_array($status, ['active', 'inactive'], true))
            ->values()
            ->all();

        //--------------------------------------------------
        // STEP 2
        // Query database dengan filter multi-pilihan
        //--------------------------------------------------
        $paginator = $this->categoryService->paginateForAdmin(
            $page,
            $perPage,
            $search,
            $categoryIds,
            $statuses,
        );

        //--------------------------------------------------
        // STEP 3
        // Kembalikan JSON untuk AJAX
        //--------------------------------------------------
        return response()->json([
            'success' => true,
            'data' => collect($paginator->items())->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'icon' => $row->icon ?? '📦',
                'color' => $row->color ?? '#059669',
                'description' => $row->description,
                'display_order' => (int) $row->display_order,
                'is_active' => (bool) $row->is_active,
                'status_label' => $row->is_active ? 'Aktif' : 'Nonaktif',
                'edit_url' => route('admin.kategori.edit', $row->id),
            ]),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.kategori.create', [
            'emojiPresets' => ['🌱', '🌾', '💊', '🔧', '🌿', '🍅', '💧', '🌽'],
            'colorPresets' => [
                ['label' => 'Hijau', 'value' => '#059669'],
                ['label' => 'Kuning', 'value' => '#d97706'],
                ['label' => 'Merah', 'value' => '#dc2626'],
                ['label' => 'Biru', 'value' => '#2563eb'],
            ],
        ]);
    }

    public function edit(int $id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (! $category) {
            abort(404);
        }

        return view('admin.kategori.edit', [
            'category' => $category,
            'emojiPresets' => ['🌱', '🌾', '💊', '🔧', '🌿', '🍅', '💧', '🌽'],
            'colorPresets' => [
                ['label' => 'Hijau', 'value' => '#059669'],
                ['label' => 'Kuning', 'value' => '#d97706'],
                ['label' => 'Merah', 'value' => '#dc2626'],
                ['label' => 'Biru', 'value' => '#2563eb'],
            ],
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['display_order'] = $data['display_order'] ?? 0;

        //--------------------------------------------------
        // STEP 2
        // Simpan kategori melalui service
        //--------------------------------------------------
        try {
            $this->categoryService->create($data);
        } catch (Exception $e) {
            Log::error('KategoriController::store failed', ['message' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['name' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput()->withErrors(['name' => $e->getMessage()]);
        }

        //--------------------------------------------------
        // STEP 3
        // Redirect / JSON setelah berhasil
        //--------------------------------------------------
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
            ]);
        }

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateCategoryRequest $request, int $id)
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        //--------------------------------------------------
        // STEP 2
        // Update kategori melalui service
        //--------------------------------------------------
        try {
            $this->categoryService->update($id, $data);
        } catch (Exception $e) {
            Log::error('KategoriController::update failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['name' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput()->withErrors(['name' => $e->getMessage()]);
        }

        //--------------------------------------------------
        // STEP 3
        // Redirect / JSON setelah berhasil
        //--------------------------------------------------
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, int $id)
    {
        try {
            $result = $this->categoryService->toggleActive($id);
        } catch (Exception $e) {
            Log::error('KategoriController::toggleStatus failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 404);
            }

            return back()->withErrors(['name' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'is_active' => $result['is_active'],
                'status_label' => $result['status_label'],
            ]);
        }

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', $result['message']);
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->categoryService->delete($id);
        } catch (QueryException $e) {
            Log::error('KategoriController::destroy failed', ['message' => $e->getMessage(), 'id' => $id]);

            $message = 'Kategori tidak bisa dihapus karena masih terhubung ke data lain.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->withErrors(['name' => $message]);
        } catch (Exception $e) {
            Log::error('KategoriController::destroy failed', ['message' => $e->getMessage(), 'id' => $id]);

            $status = str_contains($e->getMessage(), 'masih memiliki produk') ? 422 : 404;

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $status);
            }

            return back()->withErrors(['name' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
