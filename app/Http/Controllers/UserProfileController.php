<?php

namespace App\Http\Controllers;

use App\Events\ProfileViewed;
use App\Http\Resources\UserResource;
use App\Models\Ban;
use App\Models\Chat;
use App\Models\ProfileView;
use App\Models\Report;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class UserProfileController extends Controller
{
    public function index(){
        $profile = UserProfile::get();
        return response()->json($profile);
    }

    public function profileByGuest(Request $request,$username){
        //return $this->getFullProfile($username,[0]);
        $result = $this->getFullProfile($username,[0]);
        if (!$result) {
            return response()->json(['user'=>$result,'unlockChat'=>false]);
        }
        return response()->json([
            'user'=>new UserResource($result),
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
                    'user'=>new UserResource($user),
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
        // ProfileViewed::dispatch($user->id, $result->id);

        return response()->json(['user'=>new UserResource($result),'unlockChat'=>$unlockChat]);
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
        ->NotBanned()
        ->NotShadowBanned()
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
        // if($user){
        //     $user = new UserResource($user);
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
            ProfileView::where('viewer_id', $viewerId)
            ->where('viewed_id', $viewedId)
            ->delete();

            ProfileView::create([
                'viewer_id' => $viewerId,
                'viewed_id' => $viewedId
            ]);
        }
    }

    public function getLastViews(Request $request){
        $lastViewers = ProfileView::withWhereHas(
            'viewer' ,function ($query) use ($request) {
                $query->select(
                    'id',
                    'name',
                    'role',
                    'created_at',
                    'profile_picture_id',
                )->hasProfilePicture()
                ->forOppositeRole($request->user()->role);
            }
        )
        ->where('viewed_id', $request->user()->id)        // Only views of this profile
        ->latest()                             // Newest first
        // ->take(5)                             // Limit to 5
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
        // $data1 = DB::table('user_profiles')
        // ->join('users', 'user_profiles.user_id', '=', 'users.id')
        // ->where('users.role', User::ROLE_KING)
        // ->update(['user_profiles.credits' => 100]);
        if($request->user()->role == User::ROLE_KING){
            $data1 = UserProfile::where('user_id',$request->user()->id)->first();
            $data1->update(['credits' => 100]);
            return response()->json($data1);
        }
        else{
            return response()->json('not good');
        }
        
    }
    
    public function reportUser(Request $request)
    {
        $validator = Validator::make(
            $request->all(),[
                'reported_user_id' => 'required|exists:users,id',
                'reason' => 'required|string|max:1000',
                //'additional_info' => 'nullable|string|max:1000',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid Data'], 422);
        }

        if(auth()->id() == $request->reported_user_id){
            return response()->json(['message' => 'Cannot Report self'], 422);
        }

        if(Report::where('reporter_id',auth()->id())->where('reported_user_id',$request->reported_user_id)->first()){
            return response()->json(['message' => 'Already Reported'], 422);
        } 
        $report = Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $request->reported_user_id,
            'reason' => $request->reason,
            //'additional_info' => $request->additional_info ?? null,
            
        ]);
    
        return response()->json([
            'message' => 'Report submitted successfully',
            'data' => $report
        ], 201);
    }

    public function banReport($username){
        $ban = Ban::whereHas('user', function ($query) use ($username) {
            $query->where('name', $username);
        })->first();
        if(!$ban){
            return response()->json(['message'=> 'no'], 404);
        }
        if($ban->isPermanent()){
            return response()->json(['message'=> 'You have been permanently banned'],200);
        }
        if($ban->isTemporary()){
            $date = Carbon::parse($ban->expired_at);
            return response()->json(['message'=> 'You have been temporarily banned until '.$date->format('F j, Y \a\t g:i A')],200);
        }
    }
}
