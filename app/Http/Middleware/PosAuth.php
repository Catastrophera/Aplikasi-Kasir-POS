<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PosAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::get('pos_authenticated')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
