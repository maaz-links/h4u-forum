<?php

namespace App\Http\Controllers;

use App\Events\ProfileViewed;
use App\Http\Resources\UserResource;
use App\Models\Ban;
use App\Models\Chat;
use App\Models\ProfileView;
use App\Models\Report;
use App\Models\ReportChat;
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
            'canReport'=>false,
            'showSocial'=>false,
        ]);
    }

    public function profileByUser(Request $request,$username){

        $user = $request->user(); //DONT USE $request->user()->role()

        // LOOK AT YOUR OWN PROFILE
        if(($user->name === $username) && $user->profile_picture_id){
            $user = User::with('profile')->where('id', '=', $request->user()->id)->first();
            return response()->json(
                [
                    'user'=>new UserResource($user),
                    'unlockChat'=>false,
                    'canReport'=>false,
                    'showSocial'=>true,
                ]
            );
        }
        $result = $this->getFullProfile($username,[0,1],$user->role);
        if($result == "Terrible"){
            return response()->json('Terrible code',500);
        }
        if (!$result) {
            return response()->json(['user'=>$result,'unlockChat'=>false,'canReport'=>false,'showSocial'=>false,]);
        }

        //If current user (not target user's profile) is not activated
        if(!$user->hasActivatedProfile()){
            $unlockChat = false;
            $canReport = false;
            $showSocial = false;
        }else{
            $existingChat = Chat::findBetweenUsers($user->id, $result->id);
            $unlockChat = $existingChat ? false : true;

            //Only show Social Links if chat is unlocked with other user.
            $showSocial = $existingChat?->unlocked ? true : false;
            $canReport = true;
        }
        $this->recordProfileView($user->id, $result->id);
        // ProfileViewed::dispatch($user->id, $result->id);

        return response()->json(
            ['user'=>new UserResource($result),'unlockChat'=>$unlockChat,'canReport'=>$canReport,'showSocial'=>$showSocial]);
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
                ->NotBanned()
                ->NotShadowBanned()
                ->forOppositeRole($request->user()->role);
            }
        )
        ->where('viewed_id', $request->user()->id)        // Only views of this profile
        ->latest()                             // Newest first
        ->take(5)                             // Limit to 5
        ->get();                               // Execute query
        return response()->json($lastViewers);
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
            return response()->json(['message' => 'Dati non validi'], 422); // 'Invalid Data'
        }

        if(auth()->id() == $request->reported_user_id){
            return response()->json(['message' => 'Non puoi segnalare te stesso'], 422); // 'Cannot Report self'
        }

        if(Report::where('reporter_id',auth()->id())->where('reported_user_id',$request->reported_user_id)->first()){
            return response()->json(['message' => 'Utente già segnalato'], 422); // 'Already Reported'
        } 

        $report = Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $request->reported_user_id,
            'reason' => $request->reason,
            //'additional_info' => $request->additional_info ?? null,
        ]);

        return response()->json([
            'message' => 'Segnalazione inviata con successo', // 'Report submitted successfully'
            'data' => $report
        ], 201);
    }

    public function reportChat(Request $request)
    {
        $validator = Validator::make(
            $request->all(),[
                'reported_chat_id' => 'required|exists:chats,id',
                'reason' => 'required|string|max:1000',
                //'additional_info' => 'nullable|string|max:1000',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => 'Dati non validi'], 422); // 'Invalid Data'
        }

        if(ReportChat::where('reporter_id',auth()->id())->where('reported_chat_id',$request->reported_chat_id)->first()){
            return response()->json(['message' => 'Chat già segnalata'], 422); // 'Already Reported'
        } 

        $report = ReportChat::create([
            'reporter_id' => auth()->id(),
            'reported_chat_id' => $request->reported_chat_id,
            'reason' => $request->reason,
            //'additional_info' => $request->additional_info ?? null,
        ]);

        return response()->json([
            'message' => 'Segnalazione inviata con successo', // 'Report submitted successfully'
            'data' => $report
        ], 201);
    }

    public function banReport($username)
    {

        $ban = Ban::whereHas('user', function ($query) use ($username) {
            $query->where('name', $username);
        })->first();

        if (!$ban) {
            return response()->json(['message' => 'nessun ban trovato'], 404);
        }

        if ($ban->isPermanent()) {
            return response()->json(['message' => 'Sei stato bannato permanentemente'], 200);
        }

        if ($ban->isTemporary()) {
            $date = Carbon::parse($ban->expired_at);
            return response()->json([
                // 'message' => 'You have been temporarily banned until ' . $date->format('F j, Y \a\t g:i A')
                'message' => 'Sei stato bannato temporaneamente fino al ' . $date->format('j F Y \a\l\l\e g:i A')
            ], 200);
        }

        return response()->json(['message' => 'Nessun ban attivo trovato'], 200);
    }
}
