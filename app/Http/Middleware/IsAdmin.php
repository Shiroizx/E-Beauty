<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if user has admin role (you can customize this based on your user table structure)
        // Option 1: Check by email
        if (auth()->user()->email === 'admin@ebeauty.com') {
            return $next($request);
        }

        // Option 2: If you have a role column in users table
        // if (auth()->user()->role === 'admin') {
        //     return $next($request);
        // }

        // Option 3: If you have a is_admin boolean column
        // if (auth()->user()->is_admin) {
        //     return $next($request);
        // }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}
