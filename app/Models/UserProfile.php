<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // 'available_services',
        // 'personal_interests',
        'gender',
        'description',
        'facebook',
        'instagram',
        'telegram',
        'tiktok',
        'onlyfans',
        'personal_website',
        'height',
        'shoe_size',
        'eye_color',
        'dress_size',
        'weight',
        'is_user_model',
        'top_profile',
        'verified_female',
        'verified_profile',
        'visibility_status',
        'notification_pref',
        'travel_available',
        'credits',
        'nationality',
        'country_id',
        'province_id',
        'warnings',
    ];
    // protected $appends = ['available_services','personal_interests','my_languages','country_name', 'province_name'];

    public function getAvailableServicesAttribute()
    {
        return $this->hostess_services()->pluck('hostess_services.id')->toArray();
    }

    public function getPersonalInterestsAttribute()
    {
        return $this->interests()->pluck('interests.id')->toArray();
    }

    public function getMyLanguagesAttribute()
    {
        return $this->spoken_languages()->pluck('spoken_languages.id')->toArray();
    }

    public function getCountryNameAttribute()
    {
        return $this->country() ? $this->country()->value('name') : null;
    }

    public function getProvinceNameAttribute()
    {
        return $this->province() ? $this->province()->value('name') : null;
    }

    public function country()
    {
        return $this->belongsTo(EuropeCountry::class);
    }
    public function province()
    {
        return $this->belongsTo(EuropeProvince::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hostess_services()
    {
        return $this->belongsToMany(HostessService::class,
        'hostess_service_pivot', 'user_profile_id', 'hostess_service_id')
        ->withTimestamps();
    }
    public function interests()
    {
        return $this->belongsToMany(Interest::class,
         'profile_interests', 'profile_id', 'interest_id')
         ->withTimestamps();
    }
    public function spoken_languages()
    {
        return $this->belongsToMany(SpokenLanguage::class,
         'spoken_language_user_profile', 'profile_id', 'language_id')
         ->withTimestamps();
    }
}