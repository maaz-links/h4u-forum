<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetDailyMsgLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Check if we've already reset credits today
        if($request->user()->role == User::ROLE_HOSTESS)
        {
            $lastReset = cache()->get('last_credit_reset');
            //dd($lastReset,Carbon::parse($lastReset)->isToday());
            if (!$lastReset || !Carbon::parse($lastReset)->isToday()) {
                // Reset all users' credits to 5
                DB::table('user_profiles')
                ->join('users', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.role', User::ROLE_HOSTESS)
                ->update(['user_profiles.credits' => 5]);
                
                // Update the last reset timestamp
                cache()->forever('last_credit_reset', now());
            }
        }
        
        return $next($request);
    }
}
