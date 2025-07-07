<?php

namespace App\Http\Middleware;

use Auth;
use Carbon\Carbon;
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
    // public function handle($request, Closure $next)
    // {
    //     // Grab the user once (cheap) instead of calling auth() multiple times
    //     /** @var \App\Models\User|null $user */
    //     $user = $request->user();           // Same as auth()->user()

    //     // Not logged in? Nothing to do.
    //     if (! $user) {
    //         //return $next($request);         // Avoid the DB hit below
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     // Block banned accounts fast
    //     if ($user->isBanned()) {
    //         return response()->json(['message' => 'Banned'], 401);
    //     }

    //     /**
    //      * Touch `last_seen` only when it’s **stale** (e.g. >60 s old).
    //      * ‑ cuts write volume dramatically on pages that make many Ajax hits.
    //      */
    //     $windowMinutes = 1;
    //     $lastSeen = Carbon::parse($user->last_seen);
    //     $cutoff = Carbon::now()->subMinutes($windowMinutes);
    //     //return $this->last_seen->gte(Carbon::now()->subMinutes($window));
        
    //     if (
    //         $user->last_seen === null ||
    //         !$lastSeen->greaterThanOrEqualTo($cutoff) 
    //     ) {
    //         // saveQuietly() avoids model events / timestamps
    //         $user->forceFill(['last_seen' => now()])->saveQuietly();
    //     }
        

    //     return $next($request);
    // }
}
