<?php

namespace App\Helpers;

class CookieHelper
{
    public static function set(string $name, string $value, int $expiry = 3600, string $path = '/', bool $secure = false, bool $httponly = true): bool
    {
        $expiryTime = time() + $expiry; 
        
        return setcookie($name, $value, [
            'expires' => $expiryTime,
            'path' => $path,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => 'Lax'
        ]);
    }

    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    public static function delete(string $name, string $path = '/'): bool
    {
        return setcookie($name, '', time() - 3600, $path);
    }
}