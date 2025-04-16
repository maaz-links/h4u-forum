<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function user_profiles()
    {
        return $this->belongsToMany(UserProfile::class,
         'profile_interests', 'interest_id', 'profile_id')
         ->withTimestamps();
    }
}
