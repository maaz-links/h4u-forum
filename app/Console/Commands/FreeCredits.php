<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

class FreeCredits extends Command
{
    protected $signature = 'credits:free';
    protected $description = 'Free credits for males';

    public function handle()
    {
        $amount = 50;
            
            $updated = DB::table('user_profiles')
                ->join('users', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.role', User::ROLE_KING)
                ->where('user_profiles.credits', '<',$amount)
                ->update(['user_profiles.credits' => $amount]);
                
        
        
        return 0;
    }
}