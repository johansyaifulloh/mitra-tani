<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokoAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('toko_logged_in')) {
            return redirect()
                ->route('toko.login', ['redirect' => $request->route()->getName()])
                ->with('info', 'Silakan masuk terlebih dahulu untuk melanjutkan checkout.');
        }

        return $next($request);
    }
}
