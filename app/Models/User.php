<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Builders\UserQueryBuilder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_HOSTESS = 'HOSTESS';
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_KING = 'KING';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'newsletter',
        'role',
        'dob',
        'profile_picture_id',
        'otp',
        'otp_expires_at',
        'dummy_id',
        //user_profile'
    ];
    // protected $appends = ['pfp_url'];
    // public function getPfpUrlAttribute(){
    //     return $this->profilePictureId()->value('path');
    // }
    // protected $appends = ['rating'];

    public function getRatingAttribute(){
        return $this->reviewsReceived()->avg('rating') ?? 0.0;
    }

    public function profilePictureId()
    {
        return $this->belongsTo(Attachment::class, 'profile_picture_id');
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
    // public function chats()
    // {
    //     // $case1 = $this->hasMany(Chat::class, 'user1_id');
    //     // return !$case1 ? $this->hasMany(Chat::class, 'user1_id') : $case1;
    //     return $this->hasMany(Chat::class, 'user1_id');
    // }

    // public function chatsAsUser1()
    // {
    //     return $this->hasMany(Chat::class, 'user1_id');
    // }
    
    // public function chatsAsUser2()
    // {
    //     return $this->hasMany(Chat::class, 'user2_id');
    // }
    
    // // Combined accessor
    // public function getChatsAttribute()
    // {
    //     if (!$this->relationLoaded('chatsAsUser1') || !$this->relationLoaded('chatsAsUser2')) {
    //         $this->load('chatsAsUser1', 'chatsAsUser2');
    //     }
        
    //     return $this->chatsAsUser1->merge($this->chatsAsUser2);
    // }
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user')
            ->withPivot('is_archived', 'archived_at')
            ->withTimestamps();
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get all reviews this user has received (as reviewed user)
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    protected static function booted()
    {
        static::deleting(function ($parent) {
            //dd($parent,$parent->securefile);
            $parent->attachments->each(function ($child) {
                $child->delete();
            });
        });
    }

    // In app/Models/User.php
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    // app/Models/User.php
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expires_at',
        'dummy_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function newEloquentBuilder($query): UserQueryBuilder
    {
        //dd('ok');
        return new UserQueryBuilder($query);
    }

    //DUMMY
    public function dummySettings()
    {
        return $this->belongsTo(FakeProfileSetting::class, 'dummy_id');
    }

    public function isDummy(){
        return $this->dummy_id ? true: false;
    }

    //BANNING
    public function bans()
    {
        return $this->hasOne(Ban::class);
    }
    
    public function activeBan()
    {
        return $this->bans()
            ->where(function($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->latest()
            ->first();
    }
    
    public function isBanned(): bool
    {
        return !is_null($this->activeBan());
    }
    
    public function isPermanentlyBanned(): bool
    {
        $ban = $this->activeBan();
        return $ban && $ban->isPermanent();
    }
    
    public function isTemporarilyBanned(): bool
    {
        $ban = $this->activeBan();
        return $ban && $ban->isTemporary();
    }
    
    public function ban(array $options = []): Ban
    {
        return $this->bans()->create([
            'expired_at' => $options['expired_at'] ?? null,
            'reason' => $options['reason'] ?? null,
        ]);
    }
    
    public function unban(): void
    {
        $this->bans()->delete();
    }

    //Shadowban
    public function shadow_bans()
    {
        return $this->hasOne(ShadowBan::class);
    }

    public function activeShadowBan()
    {
        return $this->shadow_bans()
            ->where(function($query) {
                $query->where('expired_at', '>', now());
            })
            ->latest()
            ->first();
    }

    public function isShadowBanned(): bool
    {
        return !is_null($this->activeShadowBan());
    }

    public function removeShadowBan(): void
    {
        $this->shadow_bans()->delete();
    }


    //ADMIN ROLES
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission($permission)
    {
        return $this->roles()->whereHas('permissions', function($q) use ($permission) {
            $q->where('slug', $permission);
        })->exists();
    }
}
