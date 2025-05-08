<?php

namespace App\Models;

use App\Models\Builders\ChatQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['user1_id', 'user2_id', 'user1_archived','user2_archived','unlocked','temp'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id')
                    // ->whereIn('role', [User::ROLE_KING, User::ROLE_HOSTESS])
                    ;
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id')
                    // ->whereIn('role', [User::ROLE_KING, User::ROLE_HOSTESS])
                    ;
    }

    protected static function booted()
    {
        static::saving(function ($chat) {
            $user1 = User::find($chat->user1_id);
            $user2 = User::find($chat->user2_id);
            if ($user1 && !in_array($user1->role, [User::ROLE_KING, User::ROLE_HOSTESS])) {
                throw new \Exception("Foreign Key can only be associated with KING or HOSTESS users");
            }
            if ($user2 && !in_array($user2->role, [User::ROLE_KING, User::ROLE_HOSTESS])) {
                throw new \Exception("Foreign Key can only be associated with KING or HOSTESS users");
            }
        });
    }

    public function otherUser()
    {
        return auth()->id() === $this->user1_id ? $this->user2 : $this->user1;
    }

    public static function findBetweenUsers($userAId, $userBId,$unlocked = false,$timePassed = null)
    {
        return self::where(function ($query) use ($userAId, $userBId,$unlocked,$timePassed) {
            $query->where('user1_id', $userAId)
                ->where('user2_id', $userBId)
                ->when($unlocked, function ($query)
                    {$query->where('unlocked', true);
                    })
                    ->when($timePassed, function ($query) use ($timePassed)
                    {$query->where('created_at', '<=', $timePassed);
                    });
                
        })->orWhere(function ($query) use ($userAId, $userBId) {
            $query->where('user1_id', $userBId)
                ->where('user2_id', $userAId);
        })->first();
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_user')
            ->withPivot('is_archived', 'archived_at')
            ->withTimestamps();
    }

    // Helper method to check if user is participant
    public function hasParticipant($userId): bool
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    public function newEloquentBuilder($query): ChatQueryBuilder
    {
        return new ChatQueryBuilder($query);
    }
}