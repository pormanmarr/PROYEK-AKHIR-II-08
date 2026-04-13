<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Akun;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $akun_id = session('akun_id');
        
        if (!$akun_id) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $akun = Akun::find($akun_id);

        if (!$akun || !$akun->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini. Hanya super admin yang dapat mengelola data guru.');
        }

        return $next($request);
    }
}
