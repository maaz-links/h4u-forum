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
        //'gender',
        'description',
        'whatsapp',
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

    public function setSocialLinks(array $links = [])
    {
        $allowed = [
            'whatsapp',
            'facebook',
            'instagram',
            'telegram',
            'tiktok',
            'onlyfans',
            'personal_website'
        ];
    
        foreach ($allowed as $key) {
            if (array_key_exists($key, $links)
                 //&& $links[$key] !== null
                ) {
                $this->{$key} = $links[$key];
            }
        }
    }

    public function getUnlockCost(){
        $cost = config('h4u.chatcost.standard');
        if($this->verified_profile){
            $cost = config('h4u.chatcost.verified');
        }
        if($this->top_profile){
            $cost = config('h4u.chatcost.topprofile');
        }
        if($this->top_profile && $this->verified_profile){
            $cost = config('h4u.chatcost.verified_topprofile');
        }
        return $cost;
    }
    
    public function getAvailableServicesAttribute()
    {
        return $this->hostess_services()->pluck('hostess_services.id')->toArray();
    }

    public function getMyProfileTypesAttribute()
    {
        return $this->profileTypes()->pluck('profile_types.id')->toArray();
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

    public function profileTypes()
    {
        return $this->belongsToMany(ProfileType::class,
        'profile_profile_type_pivot','user_profile_id','profile_type_id'
        );
    }

    protected $requiredFieldsFemale = [
        'user_id',
        'description',
        'height',
        'shoe_size',
        'eye_color',
        'dress_size',
        'weight',
        'nationality',
        //'country_id',
        'province_id',
        'travel_available',
    ];

    protected $requiredFieldsMale = [
        'user_id',
        'description',
        'nationality',
        //'country_id',
        'province_id',
    ];
    
    protected $requiredRelationsFemale = [
        'hostess_services',  // At least one service required
        'interests',         // At least one interest required
        'spoken_languages',  // At least one language required
        //'profileTypes',      // At least one profile type required
    ];
    protected $requiredRelationsMale = [
        'spoken_languages',  // At least one language required
        //'profileTypes',      // At least one profile type required
    ];

    public function getProfileCompletionAttribute(): float
    {
        $role = $this->user()->value('role');
    
        if ($role === User::ROLE_HOSTESS) {
            $requiredFields = $this->requiredFieldsFemale;
            $requiredRelations = $this->requiredRelationsFemale;
        } elseif ($role === User::ROLE_KING) {
            $requiredFields = $this->requiredFieldsMale;
            $requiredRelations = $this->requiredRelationsMale;
        } else {
            // Fallback: no specific fields required
            return 100;
        }
    
        $totalFields = count($requiredFields);
        $totalRelations = count($requiredRelations);
        $totalItems = $totalFields + $totalRelations;
    
        $completed = 0;
    
        // Check regular fields
        foreach ($requiredFields as $field) {
            if ($this->$field !== NULL) {
                $completed++;
            }
        }
    
        // Check relationships
        foreach ($requiredRelations as $relation) {
            if ($this->$relation()->count() > 0) {
                $completed++;
            }
        }
    
        return $totalItems > 0 ? round(($completed / $totalItems) * 100, 0) : 100;
    }
}