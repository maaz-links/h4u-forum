<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use League\Flysystem\Visibility;

class SearchController extends Controller
{
    public function searchByGuest(Request $request){
        return $this->search($request,[0]);
    }

    public function searchByUser(Request $request){

        $user = $request->user(); //DONT USE $request->user()->role()
    
        return $this->search($request,[0,1],$user->role);
    }

    protected function search(Request $request,$check_visibility,$role = User::ROLE_KING)
    {
        $perPage = $request->per_page ?? 50;

        $minage = $request->minage ?? false;
        $maxage = $request->maxage ?? false;

        //$cost = $request->cost ?? false;
        $chatcost = config('h4u.chatcost');


        $language = $request->language ?? false;

        $rawCostQ ="CASE 
                    WHEN top_profile = 1 AND verified_profile = 1 THEN {$chatcost['verified_topprofile']}
                    WHEN top_profile = 1 THEN {$chatcost['topprofile']}
                    WHEN verified_profile = 1 THEN {$chatcost['verified']}
                    ELSE {$chatcost['standard']}
                END";

        //$visibilityStatuses = !empty($check_visibility) ? $check_visibility : [0, 1];
        $users = User::withWhereHas('profile', function ($query)
        use ($check_visibility, $request,$language,$rawCostQ) {
            $query->select(
                'id',
                'user_id',
                'country_id',
                'province_id',
                'top_profile',
                'verified_profile',
                'visibility_status',

                // // Add computed cost column
                //     \DB::raw("CASE 
                //     WHEN top_profile = 1 AND verified_profile = 1 THEN {$chatcost['verified_topprofile']}
                //     WHEN top_profile = 1 THEN {$chatcost['topprofile']}
                //     WHEN verified_profile = 1 THEN {$chatcost['verified']}
                //     ELSE {$chatcost['standard']}
                // END as cost")
            )
            ->whereIn('visibility_status', $check_visibility)
            ->when($request->top_profile, function($q) use ($request) {
                $q->where('top_profile', $request->top_profile);
            })
            ->when($request->verified_profile, function($q) use ($request) {
                $q->where('verified_profile', $request->verified_profile);
            })
            ->when($request->province_id, function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            })
            ->when($request->cost, function($q) use ($request, $rawCostQ) {
                $q->whereRaw($rawCostQ." <= ?", [$request->cost]);
            })

            //LANGUAGE FILTER BY ID
            ->when($language, function($q) use ($language) {
                $q->whereHas('spoken_languages', function($q) use ($language) {
                    $q->where('spoken_languages.id', $language);
                });
            });
        })
        
        ->with([
            'profile' => function($query) {
                $query->select(
                    'id',
                    'user_id',
                    'country_id',
                    'province_id',
                    'top_profile',
                    'verified_profile',
                    'visibility_status'
                );
            },
            'profile.profileTypes' // or specify columns for profileTypes if needed
        ])
        
        // Add the hostess filter condition
            ->when($request->hostess, function($query) {
                $query->whereHas('profile.profileTypes', function($q) {
                    $q->where('name', 'Hostess');
                });
            })
            ->when($request->wingwoman, function($query) {
                $query->whereHas('profile.profileTypes', function($q) {
                    $q->where('name', 'Wingwoman');
                });
            })
            ->when($request->sugarbaby, function($query) {
                $query->whereHas('profile.profileTypes', function($q) {
                    $q->where('name', 'Sugarbaby');
                });
            })

        ->when($minage !== false || $maxage !== false, function($q) use ($minage, $maxage) {
            $q->where(function($query) use ($minage, $maxage) {
                if ($minage !== false) {
                    $minDate = now()->subYears($minage)->format('Y-m-d');
                    $query->where('dob', '<=', $minDate);
                }
                if ($maxage !== false) {
                    $maxDate = now()->subYears($maxage + 1)->format('Y-m-d');
                    $query->where('dob', '>=', $maxDate);
                }
            });
        })
        ->select('id', 'name', 'email', 'dob', 'role', 'last_seen', 'profile_picture_id')
        ->hasProfilePicture()
        ->NotBanned()
        ->NotShadowBanned()
        ->forOppositeRole($role)
        
        //SORT BY COST
        ->when(false, function($query) use ($chatcost) {
            $query->orderByRaw(
                "CASE 
                    WHEN EXISTS (SELECT 1 FROM user_profiles WHERE user_profiles.user_id = users.id AND user_profiles.top_profile = 1 AND user_profiles.verified_profile = 1) THEN ?
                    WHEN EXISTS (SELECT 1 FROM user_profiles WHERE user_profiles.user_id = users.id AND user_profiles.top_profile = 1) THEN ?
                    WHEN EXISTS (SELECT 1 FROM user_profiles WHERE user_profiles.user_id = users.id AND user_profiles.verified_profile = 1) THEN ?
                    ELSE ?
                END " . 'DESC',
                [
                    $chatcost['verified_topprofile'],
                    $chatcost['topprofile'],
                    $chatcost['verified'],
                    $chatcost['standard']
                ]
            );
    
        })
        // ->when($request->sort === "popular", function($query) {
        //     $query->leftJoin('chats', function($join) {
        //         $join->on(function($query) {
        //             $query->on('users.id', '=', 'chats.user1_id')
        //                   ->orOn('users.id', '=', 'chats.user2_id');
        //         })
        //         ->where('chats.unlocked', '=', 1);
        //     })
        //     ->select([
        //         'users.id',
        //         'users.name',
        //         'users.email',
        //         'users.dob',
        //         'users.role',
        //         'users.last_seen',
        //         'users.profile_picture_id',
        //         \DB::raw('COUNT(DISTINCT chats.id) as unlocked_chats_count')
        //     ])
        //     ->groupBy('users.id')
        //     ->orderBy('unlocked_chats_count', 'desc');
        // })
        ->when($request->sort === "popular", function($query) {
            $query->withCount([
                'chats as popularity_count' => function($q) {
                    $q->where('unlocked', 1);
                }
            ])->orderBy('popularity_count', 'desc');
        })
        
        ->when($request->sort === "rating", function($query) {
            $query->withAvg(['reviewsReceived as average_rating'], 'rating')
                  ->orderBy('average_rating', 'desc');
        })
        ->when($request->sort === "newest" , function($query) {
            $query->orderBy('created_at', 'desc');
        })
        // ->get();
        ->paginate($perPage);

        
        return response()->json($users);
    }
}
