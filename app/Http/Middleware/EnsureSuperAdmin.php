<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Hanya Super Admin yang dapat mengakses halaman ini.');
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}
