<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // if (auth()->check() && auth()->user()->isBanned()) {
        //     $ban = auth()->user()->activeBan();
            
        //     if ($ban->isPermanent()) {
        //         auth()->logout();
        //         return redirect()->route('banned')->with('error', 
        //             'Your account has been permanently banned. Reason: ' . $ban->reason);
        //     }
            
        //     if ($ban->isTemporary()) {
        //         auth()->logout();
        //         return redirect()->route('banned')->with('error', 
        //             'Your account has been temporarily banned until ' . 
        //             $ban->expired_at->format('Y-m-d H:i:s') . 
        //             '. Reason: ' . $ban->reason);
        //     }
        // }
        if (auth()->check() && auth()->user()->isBanned()) {
            return response()->json(["message"=> "Banned"], 401);
        }

        return $next($request);
    }
}
