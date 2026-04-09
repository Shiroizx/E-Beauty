<?php

namespace App\Http\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AuthIntended
{
    public static function relativeFromRequest(Request $request): string
    {
        $path = $request->path();
        $rel = $path === '' ? '/' : '/' . $path;
        $qs = $request->getQueryString();
        if (is_string($qs) && $qs !== '') {
            $rel .= '?'.$qs;
        }

        return $rel;
    }

    public static function isSafeRelative(string $relative): bool
    {
        if ($relative === '' || strlen($relative) > 512) {
            return false;
        }
        if (! str_starts_with($relative, '/') || str_starts_with($relative, '//')) {
            return false;
        }
        if (preg_match('/[\r\n\0]/', $relative)) {
            return false;
        }
        if (str_contains($relative, '\\')) {
            return false;
        }

        return true;
    }

    /**
     * Store post-login destination (same-app only, via Laravel url()).
     */
    public static function putIntendedFromRelative(Request $request, string $relative): void
    {
        if (! self::isSafeRelative($relative)) {
            return;
        }

        $request->session()->put('url.intended', URL::to($relative));
    }

    public static function putIntendedFromQuery(Request $request): void
    {
        $raw = $request->query('redirect');
        if (! is_string($raw) || $raw === '') {
            return;
        }

        self::putIntendedFromRelative($request, $raw);
    }
}
