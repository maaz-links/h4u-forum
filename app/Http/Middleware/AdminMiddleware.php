<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!auth()->check() || auth()->user()->role !== 'ADMIN') {
        //     abort(403, 'Unauthorized. Admin access required.');
        // }
        if (Auth::check() && Auth::user()->role !== 'ADMIN') {
            Auth::logout(); // Force logout
            
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Administrator access only']);
        }
        return $next($request);
    }
}
