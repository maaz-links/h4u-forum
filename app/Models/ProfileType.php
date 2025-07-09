<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileType extends Model
{
    protected $fillable = ['name'];
    public function userProfiles()
    {
        return $this->belongsToMany(UserProfile::class,
        'profile_profile_type_pivot','profile_type_id','user_profile_id'
        );
    }
}
