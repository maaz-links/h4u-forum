<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostessService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        //'slug',
        //'description',
        //'is_active',
        'display_order'
    ];

    public function user_profiles()
    {
        return $this->belongsToMany(UserProfile::class,
         'hostess_service_pivot', 'hostess_service_id', 'user_profile_id')
        ->withTimestamps();
    }
}