<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperationalAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.super.dashboard')
                ->with('error', 'Akun Super Admin tidak mengakses panel operasional.');
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}
