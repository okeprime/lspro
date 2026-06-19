<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $role = strtolower(auth()->user()->role ?? '');
        $allowedRoles = ['superadmin', 'admin'];
        
        if (!auth()->check() || !in_array($role, $allowedRoles)) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Petugas LSPro.');
        }
        
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh Superadmin.');
        }

        return $next($request);
    }
}
