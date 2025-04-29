<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ProfileView;
use App\Models\User;
use App\Models\UserProfile;
use DB;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index(){
        $profile = UserProfile::get();
        return response()->json($profile);
    }

    public function profileByGuest(Request $request,$username){
        //return $this->getFullProfile($username,[0]);
        $result = $this->getFullProfile($username,[0]);
        return response()->json([
            'user'=>$result,
            'unlockChat'=>false,
        ]);
    }

    public function profileByUser(Request $request,$username){

        $user = $request->user(); //DONT USE $request->user()->role()

        // LOOK AT YOUR OWN PROFILE
        if($user->name === $username){
            $user = User::with('profile')->where('id', '=', $request->user()->id)->first();
            return response()->json(
                [
                    'user'=>$user,
                    'unlockChat'=>false,
                ]
            );
        }
        $result = $this->getFullProfile($username,[0,1],$user->role);
        if($result == "Terrible"){
            return response()->json('Terrible code',500);
        }
        if (!$result) {
            return response()->json(['user'=>$result,'unlockChat'=>false]);
        }
        $existingChat = Chat::findBetweenUsers($user->id, $result->id);
        $unlockChat = $existingChat ? false : true;
        
        $this->recordProfileView($user->id, $result->id);

        return response()->json(['user'=>$result,'unlockChat'=>$unlockChat]);
    }

    public function getFullProfile($username,$check_visibility,$role = User::ROLE_KING){
        
        $user = User::withWhereHas(
            'profile', function ($query) use ($check_visibility) {
                $query->whereIn('visibility_status', $check_visibility);
            }
        )
        ->hasProfilePicture()
        ->forUsername($username)
        ->forOppositeRole($role)
        // ->whereHas('profile', function($q) use ($check_visibility) {
        //     $q->whereIn('visibility_status', $check_visibility);
        // })
        ->first();
        // $user = User::withVisibleProfile($check_visibility)
        // ->hasProfilePicture()
        // ->forUsername($username)
        // ->forRole($check_role)
        // //->hasProfileWithVisibility($check_visibility)
        // ->first();
        //return response()->json($user);
        // if(!$user){
        //     return false;
        // }
        return $user;
    }

    protected function recordProfileView($viewerId, $viewedId)
    {
        // Check if a view exists in the last 24 hours
        $recentViewExists = ProfileView::where('viewer_id', $viewerId)
            ->where('viewed_id', $viewedId)
            ->where('created_at', '>', now()->subDay())
            ->exists();

        if (!$recentViewExists) {
            ProfileView::create([
                'viewer_id' => $viewerId,
                'viewed_id' => $viewedId
            ]);
        }
    }

    public function getLastViews(Request $request){
        $lastViewers = ProfileView::withWhereHas(
            'viewer' ,function ($query)  {
                $query->select(
                    'id',
                    'name',
                    'role',
                    'created_at',
                    'profile_picture_id',
                )->hasProfilePicture();
            }
        )
        ->where('viewed_id', $request->user()->id)        // Only views of this profile
        ->latest()                             // Newest first
        ->take(5)                             // Limit to 5
        ->get();                               // Execute query
        return response()->json($lastViewers);
    }

    public function randomize()
    {
        // Get all user profiles
        $profiles = UserProfile::all();
//dd($profiles->only(['user_id','verified_profile','top_profile']));
        foreach ($profiles as $profile) {
            $profile->verified_profile = rand(0, 1);
            $profile->top_profile = rand(0, 1);
            $profile->save();
        }

        //$result = UserProfile::only(['user_id','verified_profile','top_profile'])->all;
        
        return response()->json([
            $profiles//'message' => 'All profiles have been randomized.',
        ]);
    }

    public function setCustomerCredits(Request $request, $amount = '100')
    {
        $data1 = DB::table('user_profiles')
        ->join('users', 'user_profiles.user_id', '=', 'users.id')
        ->where('users.role', User::ROLE_KING)
        ->update(['user_profiles.credits' => 100]);
        //$data1->update(['user_profiles.credit' => 100]);
        return response()->json($data1);
    }

}
