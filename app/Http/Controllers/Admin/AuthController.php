<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        if ($request->cookie(JwtCookie::ACCESS)) {
            try {
                JWTAuth::setToken($request->cookie(JwtCookie::ACCESS))->authenticate();

                return redirect()->route('admin.dashboard');
            } catch (Exception $e) {
                // show login
            }
        }

        return view('admin.auth.login');
    }

    public function loginSubmit(LoginRequest $request)
    {
        try {
            $data = $request->validated();
            $tokens = $this->authService->login($data['identifier'], $data['password'], ['admin', 'owner']);

            $response = redirect()->route('admin.dashboard');

            return JwtCookie::attach($response, $tokens['access_token'], $tokens['refresh_token']);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['identifier' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout(
            $request->cookie(JwtCookie::REFRESH),
            $request->cookie(JwtCookie::ACCESS),
        );

        return JwtCookie::clear(redirect()->route('admin.login'));
    }
}
