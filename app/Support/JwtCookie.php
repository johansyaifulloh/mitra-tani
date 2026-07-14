<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Cookie;

class JwtCookie
{
    public const ACCESS = 'mt_access';

    public const REFRESH = 'mt_refresh';

    public static function attach(Response|RedirectResponse $response, string $accessToken, string $refreshToken): Response|RedirectResponse
    {
        $accessMinutes = (int) config('jwt.ttl', 60);
        $refreshMinutes = (int) config('jwt.refresh_ttl', 20160);

        return $response
            ->withCookie(self::make(self::ACCESS, $accessToken, $accessMinutes))
            ->withCookie(self::make(self::REFRESH, $refreshToken, $refreshMinutes));
    }

    public static function clear(Response|RedirectResponse $response): Response|RedirectResponse
    {
        return $response
            ->withCookie(Cookie::create(self::ACCESS)->withValue('')->withExpires(time() - 3600))
            ->withCookie(Cookie::create(self::REFRESH)->withValue('')->withExpires(time() - 3600));
    }

    private static function make(string $name, string $value, int $minutes): Cookie
    {
        return Cookie::create($name)
            ->withValue($value)
            ->withExpires(time() + ($minutes * 60))
            ->withPath('/')
            ->withSecure(false)
            ->withHttpOnly(true)
            ->withSameSite('lax');
    }
}
