<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGuruRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('akun_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session('role') !== 'guru') {
            return redirect()->route('login')->with('error', 'Unauthorized: Hanya guru yang dapat akses halaman ini');
        }

        return $next($request);
    }
}
