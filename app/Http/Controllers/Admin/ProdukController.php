<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index()
    {
        // Halaman shell — data produk dimuat via AJAX (server-side pagination)
        return view('admin.produk.index');
    }

    public function fetchData(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil parameter dari request (page, search, per_page)
        // Hanya data 1 halaman yang di-query — tidak load semua baris
        //--------------------------------------------------
        $page = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('per_page', 8), 1), 50);
        $search = trim($request->query('search', ''));

        //--------------------------------------------------
        // STEP 2
        // Query database dengan JOIN kategori
        //--------------------------------------------------
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.emoji',
                'products.price',
                'products.stock',
                'products.status',
                'categories.name as category_name',
            );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%');
            });
        }

        //--------------------------------------------------
        // STEP 3
        // Paginate di server — LIMIT/OFFSET otomatis oleh Laravel
        //--------------------------------------------------
        $paginator = $query
            ->orderByDesc('products.created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        //--------------------------------------------------
        // STEP 4
        // Kembalikan JSON untuk AJAX
        //--------------------------------------------------
        return response()->json([
            'success' => true,
            'data' => collect($paginator->items())->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'emoji' => $row->emoji ?? '🌱',
                'category_name' => $row->category_name,
                'price' => (float) $row->price,
                'price_label' => 'Rp '.number_format($row->price, 0, ',', '.'),
                'stock' => (int) $row->stock,
                'status' => $row->status,
                'status_label' => $row->status === 'active' ? 'Aktif' : 'Draft',
                'edit_url' => route('admin.produk.edit', $row->id),
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
        //--------------------------------------------------
        // STEP 1
        // Mengambil kategori aktif untuk dropdown form
        //--------------------------------------------------
        $categories = DB::table('categories')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $badges = ['Baru', 'Promo', 'Terlaris'];

        //--------------------------------------------------
        // STEP 2
        // Menampilkan form tambah produk
        //--------------------------------------------------
        return view('admin.produk.create', compact('categories', 'badges'));
    }

    public function edit(int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            abort(404);
        }

        $categories = DB::table('categories')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $badges = ['Baru', 'Promo', 'Terlaris'];

        return view('admin.produk.edit', compact('product', 'categories', 'badges'));
    }

    public function store(StoreProductRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Cek kategori ada di database
        //--------------------------------------------------
        $category = DB::table('categories')
            ->where('id', $data['category_id'])
            ->first();

        if (! $category) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan.',
                    'errors' => ['category_id' => ['Kategori tidak ditemukan.']],
                ], 422);
            }

            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak ditemukan.']);
        }

        //--------------------------------------------------
        // STEP 3
        // Siapkan slug, status, dan path gambar
        //--------------------------------------------------
        $slug = Str::slug($data['name']);
        $slugExists = DB::table('products')->where('slug', $slug)->exists();

        if ($slugExists) {
            $slug = $slug.'-'.Str::random(4);
        }

        $status = ($data['status'] ?? 'active') === 'draft' ? 'draft' : 'active';
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;

        //--------------------------------------------------
        // STEP 4
        // Simpan produk ke database (transaction manual)
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            DB::table('products')->insert([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProdukController::store failed', ['message' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan produk.',
                ], 500);
            }

            return back()->withInput()->withErrors(['name' => 'Gagal menyimpan produk.']);
        }

        //--------------------------------------------------
        // STEP 5
        // Redirect / JSON setelah berhasil
        //--------------------------------------------------
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan.',
            ]);
        }

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        //--------------------------------------------------
        // STEP 1
        // Mengambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Cek produk ada di database
        //--------------------------------------------------
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.',
                ], 404);
            }

            return back()->withErrors(['name' => 'Produk tidak ditemukan.']);
        }

        if (isset($data['category_id'])) {
            $category = DB::table('categories')->where('id', $data['category_id'])->first();

            if (! $category) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kategori tidak ditemukan.',
                        'errors' => ['category_id' => ['Kategori tidak ditemukan.']],
                    ], 422);
                }

                return back()->withInput()->withErrors(['category_id' => 'Kategori tidak ditemukan.']);
            }
        }

        //--------------------------------------------------
        // STEP 3
        // Siapkan kolom yang akan diupdate
        //--------------------------------------------------
        $update = [];

        if (isset($data['category_id'])) {
            $update['category_id'] = $data['category_id'];
        }
        if (isset($data['name'])) {
            $update['name'] = $data['name'];
            $update['slug'] = Str::slug($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $update['description'] = $data['description'];
        }
        if (isset($data['price'])) {
            $update['price'] = $data['price'];
        }
        if (isset($data['stock'])) {
            $update['stock'] = $data['stock'];
        }
        if (array_key_exists('emoji', $data)) {
            $update['emoji'] = $data['emoji'] ?? '🌱';
        }
        if (array_key_exists('badge', $data)) {
            $update['badge'] = $data['badge'] ?: null;
        }
        if (isset($data['status'])) {
            $update['status'] = $data['status'] === 'draft' ? 'draft' : 'active';
        }
        if ($request->hasFile('image')) {
            $update['image_path'] = $request->file('image')->store('products', 'public');
        }

        $update['updated_at'] = now();

        //--------------------------------------------------
        // STEP 4
        // Update produk di database (transaction manual)
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            DB::table('products')->where('id', $id)->update($update);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProdukController::update failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui produk.',
                ], 500);
            }

            return back()->withInput()->withErrors(['name' => 'Gagal memperbarui produk.']);
        }

        //--------------------------------------------------
        // STEP 5
        // Redirect / JSON setelah berhasil
        //--------------------------------------------------
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.',
                ], 404);
            }

            return back()->withErrors(['name' => 'Produk tidak ditemukan.']);
        }

        $newStatus = $product->status === 'active' ? 'draft' : 'active';

        DB::beginTransaction();

        try {
            DB::table('products')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProdukController::toggleStatus failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status produk.',
                ], 500);
            }

            return back()->withErrors(['name' => 'Gagal mengubah status produk.']);
        }

        $message = $newStatus === 'active'
            ? 'Produk diaktifkan dan tampil di toko.'
            : 'Produk disimpan sebagai draft.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $newStatus,
                'status_label' => $newStatus === 'active' ? 'Aktif' : 'Draft',
            ]);
        }

        return redirect()
            ->route('admin.produk.index')
            ->with('success', $message);
    }

    public function archive(Request $request, int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.',
                ], 404);
            }

            return back()->withErrors(['name' => 'Produk tidak ditemukan.']);
        }

        DB::beginTransaction();

        try {
            DB::table('products')
                ->where('id', $id)
                ->update([
                    'status' => 'draft',
                    'updated_at' => now(),
                ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProdukController::archive failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengarsipkan produk.',
                ], 500);
            }

            return back()->withErrors(['name' => 'Gagal mengarsipkan produk.']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk diarsipkan.',
            ]);
        }

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk diarsipkan.');
    }

    public function destroy(Request $request, int $id)
    {
        //--------------------------------------------------
        // STEP 1
        // Cek produk ada di database
        //--------------------------------------------------
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.',
                ], 404);
            }

            return back()->withErrors(['name' => 'Produk tidak ditemukan.']);
        }

        //--------------------------------------------------
        // STEP 2
        // Hapus permanen dari database (transaction manual)
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            DB::table('products')->where('id', $id)->delete();

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('ProdukController::destroy failed', ['message' => $e->getMessage(), 'id' => $id]);

            $message = 'Produk tidak bisa dihapus karena sudah terhubung ke pesanan.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->withErrors(['name' => $message]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProdukController::destroy failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus produk.',
                ], 500);
            }

            return back()->withErrors(['name' => 'Gagal menghapus produk.']);
        }

        //--------------------------------------------------
        // STEP 3
        // Redirect / JSON setelah berhasil
        //--------------------------------------------------
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
