<?php

namespace App\Http\Middleware;

use App\Services\AdminNav;
use Closure;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setlocale(env('FRONTEND_LANG','en'));
        return $next($request);
    }
}
