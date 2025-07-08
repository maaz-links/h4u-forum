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
        //$visibilityStatuses = !empty($check_visibility) ? $check_visibility : [0, 1];
        $users = User::withWhereHas('profile', function ($query) use ($check_visibility, $request,$language,$chatcost) {
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
            ->when($request->cost, function($q) use ($request, $chatcost) {
                $q->whereRaw("CASE 
                    WHEN top_profile = 1 AND verified_profile = 1 THEN {$chatcost['verified_topprofile']}
                    WHEN top_profile = 1 THEN {$chatcost['topprofile']}
                    WHEN verified_profile = 1 THEN {$chatcost['verified']}
                    ELSE {$chatcost['standard']}
                END <= ?", [$request->cost]);
            })

            //LANGUAGE FILTER BY ID
            ->when($language, function($q) use ($language) {
                $q->whereHas('spoken_languages', function($q) use ($language) {
                    $q->where('spoken_languages.id', $language);
                });
            });
        })
        
        //->with('profile.spoken_languages')
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
        // ->get();
        ->paginate($perPage);

        
        return response()->json($users);
    }
}
