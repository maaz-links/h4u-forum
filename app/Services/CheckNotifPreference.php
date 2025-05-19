<?php

namespace App\Services;

use App\Models\UserProfile;

class CheckNotifPreference
{
    /**
     * Check if user's notification preference is enabled (set to 1).
     *
     * @param int $userId
     * @return bool
     */
    public static function isSMSEnabled(int $userId): bool
    {
        // $profile = UserProfile::where('user_id', $userId)->first();

        // return $profile && $profile->notification_preference == 1;
        return UserProfile::where('user_id', $userId)
                          ->where('notification_pref', 1)
                          ->exists();
    }
}
