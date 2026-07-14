<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Support\JwtCookie;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function login(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Jika sudah login (token valid), langsung redirect
        //--------------------------------------------------
        if ($request->cookie(JwtCookie::ACCESS)) {
            try {
                $user = JWTAuth::setToken($request->cookie(JwtCookie::ACCESS))->authenticate();

                // Hanya pelanggan yang langsung diarahkan; admin/owner tetap lihat form.
                if ($user && $user->role === 'customer') {
                    return $this->redirectAfterLogin($request);
                }
            } catch (Exception $e) {
                // Token invalid — tampilkan form login
            }
        }

        //--------------------------------------------------
        // STEP 2
        // Tampilkan halaman login
        //--------------------------------------------------
        return view('mobile.auth.login', [
            'redirect' => $request->query('redirect', ''),
        ]);
    }

    public function loginSubmit(LoginRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Proses login melalui service
        //--------------------------------------------------
        try {
            $tokens = $this->authService->login($data['identifier'], $data['password'], ['customer']);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['identifier' => $e->getMessage()]);
        }

        //--------------------------------------------------
        // STEP 3
        // Redirect + pasang cookie JWT
        //--------------------------------------------------
        $response = $this->redirectAfterLogin($request)
            ->with('success', 'Login berhasil.');

        return JwtCookie::attach($response, $tokens['access_token'], $tokens['refresh_token']);
    }

    public function logout(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Cabut token melalui service
        //--------------------------------------------------
        $this->authService->logout(
            $request->cookie(JwtCookie::REFRESH),
            $request->cookie(JwtCookie::ACCESS),
        );

        //--------------------------------------------------
        // STEP 2
        // Bersihkan cookie & redirect ke beranda
        //--------------------------------------------------
        return JwtCookie::clear(redirect()->route('home'));
    }

    public function register()
    {
        //--------------------------------------------------
        // STEP 1
        // Tampilkan halaman daftar akun
        //--------------------------------------------------
        return view('mobile.auth.register');
    }

    public function registerStore(RegisterRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Proses pendaftaran melalui service
        //--------------------------------------------------
        try {
            $tokens = $this->authService->register($data);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['phone' => $e->getMessage()]);
        }

        //--------------------------------------------------
        // STEP 3
        // Redirect + pasang cookie JWT
        //--------------------------------------------------
        $response = redirect()->route('home')->with('success', 'Akun berhasil dibuat.');

        return JwtCookie::attach($response, $tokens['access_token'], $tokens['refresh_token']);
    }

    private function redirectAfterLogin(Request $request)
    {
        $target = $request->input('redirect', $request->query('redirect'));

        return match ($target) {
            'checkout', 'toko.checkout.index' => redirect()->route('toko.checkout.index'),
            'toko.keranjang.index' => redirect()->route('toko.keranjang.index'),
            'toko.transaksi.index' => redirect()->route('toko.transaksi.index'),
            'toko.alamat.index' => redirect()->route('toko.alamat.index'),
            'pembayaran', 'toko.pembayaran.index' => redirect()->route('home'),
            default => redirect()->route('home'),
        };
    }
}
