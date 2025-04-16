<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpokenLanguage extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function user_profiles()
    {
        return $this->belongsToMany(UserProfile::class,
         'spoken_language_user_profile', 'language_id', 'profile_id')
         ->withTimestamps();
    }
}