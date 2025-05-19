<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

class ResetDailyCredits extends Command
{
    protected $signature = 'credits:reset-daily';
    protected $description = 'Reset daily credits for hostesses';

    public function handle()
    {
        // Only reset once per day
        $lastReset = cache()->get('last_credit_reset');

        //dd($lastReset);
        if (!$lastReset || !Carbon::parse($lastReset)->isToday()) {
            $this->info('Resetting daily free messages for hostesses...');
            
            $updated = DB::table('user_profiles')
                ->join('users', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.role', User::ROLE_HOSTESS)
                ->update(['user_profiles.credits' => 5]);
                
            //cache()->forever('last_credit_reset', now());
            //dd('wat');
            $this->info("Successfully reset free messages for {$updated} hostesses.");
            
        } else {
            $this->info('free messages were already reset today. Skipping...');
        }
        
        return 0;
    }
}