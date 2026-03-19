<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    public function handle(Request $request, Closure $next, ...$peran): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (in_array(Auth::user()->peran, $peran)) {
            return $next($request);
        }

        return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}