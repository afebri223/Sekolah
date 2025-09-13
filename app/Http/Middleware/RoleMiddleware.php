<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || $user->role !== $role) {
            abort(403, 'Akses ditolak. Anda tidak memiliki permission untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
