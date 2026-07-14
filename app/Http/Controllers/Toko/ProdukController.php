<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Support\JwtCookie;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ProdukController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CategoryService $categoryService,
    ) {}

    public function index(Request $request)
    {
        // Halaman shell — data produk dimuat via AJAX (server-side pagination)
        return view('mobile.produk.index', [
            'categories' => $this->categoryService->listForStore(),
            'loggedIn' => $this->isLoggedIn($request),
        ]);
    }

    public function fetchData(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil parameter dari request (page, search, kategori)
        //--------------------------------------------------
        $page = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('per_page', 8), 1), 24);
        $search = trim($request->query('search', ''));
        $categoryIds = collect($request->query('categories', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        //--------------------------------------------------
        // STEP 2
        // Query produk aktif + paginate di server
        //--------------------------------------------------
        $result = $this->productService->fetchActivePaginated($page, $perPage, $search, $categoryIds);

        //--------------------------------------------------
        // STEP 3
        // Kembalikan JSON untuk AJAX
        //--------------------------------------------------
        return response()->json($result);
    }

    public function show(Request $request, string $slug)
    {
        $product = $this->productService->findBySlug($slug);
        abort_unless($product, 404);

        return view('mobile.produk.show', [
            'product' => $product,
            'loggedIn' => $this->isLoggedIn($request),
        ]);
    }

    private function isLoggedIn(Request $request): bool
    {
        $token = $request->cookie(JwtCookie::ACCESS);

        if (! $token) {
            return false;
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();
            auth('api')->setUser($user);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
