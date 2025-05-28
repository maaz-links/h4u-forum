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
        //$visibilityStatuses = !empty($check_visibility) ? $check_visibility : [0, 1];
        $users = User::withWhereHas('profile', function ($query) use ($check_visibility, $request) {
            $query->select(
                'id',
                'user_id',
                'country_id',
                'province_id',
                'top_profile',
                'verified_profile',
                'visibility_status'
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
            });
        })
        ->select('id', 'name', 'email', 'role', 'profile_picture_id')
        ->hasProfilePicture()
        ->NotBanned()
        ->NotShadowBanned()
        ->forOppositeRole($role)
        ->get();

        
        return response()->json($users);
    }
}
