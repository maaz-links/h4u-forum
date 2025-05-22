<?php
// app/Models/Builders/UserQueryBuilder.php

namespace App\Models\Builders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserQueryBuilder extends Builder
{

     /**
     * Select basic user fields
     */
    public function withBasicSelect(): self
    {
        return $this->select(
            'id',
            'name',
            'email',
            'role',
            'profile_picture_id'
        );
    }

    /**
     * Filter users with visible profiles
     */
    public function withVisibleProfile(array $visibilityStatuses): self
    {
        return $this->with(['profile' => function ($query) use ($visibilityStatuses) {
            $query->whereIn('visibility_status', $visibilityStatuses);
        }]);
        // return $this->with(['profile' => function ($query) use ($visibilityStatuses) {
            // $query->whereIn('visibility_status', $visibilityStatuses);
        // }])
        // ->whereHas('profile', function($q) use ($visibilityStatuses) {
            // $q->whereIn('visibility_status', $visibilityStatuses);
        // });
    }

    /**
     * Apply profile filters from request
     */
    public function withProfileFilters(?Request $request = null): self
    {
        return $this->with(['profile' => function ($query) use ($request) {
            $query->select(
                'id',
                'user_id',
                'country_id',
                'province_id',
                'top_profile',
                'verified_profile',
                'visibility_status'
            )
            ->when($request?->top_profile, fn($q) => $q->where('top_profile', $request->top_profile))
            ->when($request?->verified_profile, fn($q) => $q->where('verified_profile', $request->verified_profile))
            ->when($request?->province_id, fn($q) => $q->where('province_id', $request->province_id));
        }]);
    }

    /**
     * Filter users with profile pictures
     */
    public function hasProfilePicture(): self
    {
        return $this->whereNotNull('profile_picture_id');
    }

    /**
     * Filter by user role
     */
    public function forRole(string $role): self
    {

        return $this->where('role', $role);
    }

    public function forRoleAny(): self
    {
        return $this->whereIn('role', [User::ROLE_HOSTESS,User::ROLE_KING]);
    }

    /**
     * Filter by opposite user role. ( Requires $role )
     */
    public function forOppositeRole(string $role): self
    {
        $rolesMap = [
            User::ROLE_KING => User::ROLE_HOSTESS,
            User::ROLE_HOSTESS => User::ROLE_KING,
        ];
        $oppositeRole = $rolesMap[$role] ?? null;
        if (!$oppositeRole) {
            abort(500, 'Terrible code for ROLE'); // or throw a custom exception if you prefer
        }
        return $this->where('role', $oppositeRole);
    }

    /**
     * Filter by username
     */
    public function forUsername(string $username): self
    {
        return $this->where('name', $username);
    }

    /**
     * Filter users who have profiles with specific visibility
     */
    public function hasProfileWithVisibility(array $visibilityStatuses, ?Request $request = null): self
    {
        return $this->whereHas('profile', function($q) use ($request) {
            $q->when($request?->top_profile, fn($q) => $q->where('top_profile', $request->top_profile))
              ->when($request?->verified_profile, fn($q) => $q->where('verified_profile', $request->verified_profile))
              ->when($request?->province_id, fn($q) => $q->where('province_id', $request->province_id));
        });
    }

   
    /**
     * Find visible user by username with all necessary filters
     */
    public function findVisibleByUsername(string $username, array $visibilityStatuses, string $role): ?User
    {
        return $this->withVisibleProfile($visibilityStatuses)
            ->hasProfilePicture()
            ->forUsername($username)
            ->forRole($role)
            ->hasProfileWithVisibility($visibilityStatuses)
            ->first();
    }

    public function NotBanned()
    {
        return $this->whereDoesntHave('bans', function($q) {
            $q->whereNull('expired_at') // Permanent bans
            ->orWhere('expired_at', '>', now()); // Active temporary bans
        });
    }

    public function scopeFake($script_id = null)
    {
        if($script_id){
            return $this->where('dummy_id', $script_id);
        }
        return $this->where('dummy_id', '!=',null);
        
    }
}