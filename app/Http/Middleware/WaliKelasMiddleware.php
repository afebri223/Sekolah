<?php
// app/Http/Middleware/WaliKelasMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class WaliKelasMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user || !$user->isWaliKelas()) {
            abort(403, 'Akses ditolak. Hanya wali kelas yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}