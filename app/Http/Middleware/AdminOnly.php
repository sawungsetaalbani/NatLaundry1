<?php
// simpan di: app/Http/Middleware/AdminOnly.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // baca cookie mentah ($_COOKIE) supaya tidak kena dekripsi cookie Laravel
        $plain = $_COOKIE['admin_token'] ?? null;
        $token = $plain ? PersonalAccessToken::findToken($plain) : null;
        $user  = $token?->tokenable;

        // belum login / token tidak valid / bukan admin -> balik ke login
        if (!$user || $user->role !== 'admin') {
            return redirect('/login?sesi=habis');
        }

        return $next($request);
    }
}