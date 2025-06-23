<?php

// app/Http/Controllers/ReviewController.php
namespace App\Http\Controllers;

use App\Events\ReviewRecieved;
use App\Models\Chat;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ReviewController extends Controller
{
    protected $dayInterval;
    protected $dayIntervalOutput;

    public function __construct()
    {
        $this->dayInterval = config('h4u.reviews.review_delay');
        $this->dayIntervalOutput = Carbon::now()->subDays(config('h4u.reviews.review_delay'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $userID = $user->id;
        $leastrating = config('h4u.reviews.minimum_rating');
        //return response()->json($leastrating,500);
        $validator = Validator::make(
            $request->all(),
            [
                'reviewed_user_id' => 'required|exists:users,id',
                'rating' => "required|integer|between:1,5",
                'comment' => 'nullable|string|max:500',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }
        
        if(Review::where('reviewer_id',$userID)
        ->where('reviewed_user_id',$request->reviewed_user_id)->first()){
            return response()->json(['formError' => ['Review Already Exists']], 422);
        }

        if($user->role == User::ROLE_HOSTESS){

            //Target must have reviewed user
            $hasReviewed = 
            Review::where('reviewer_id',$request->reviewed_user_id)
            ->where('reviewed_user_id',$userID)->first();
            if(!$hasReviewed){
                return response()->json(['formError' => ['target hasnt reviewed yet']], 422);
            }
        }
        elseif($user->role == User::ROLE_KING){

            //Chat should exist
            $existingChat = 
            Chat::findBetweenUsers($userID, $request->reviewed_user_id,true,$this->dayIntervalOutput);
            //return $existingChat;
            if(!$existingChat){
                return response()->json(['formError' => ['Chat not exist']], 422);
            }
            // if(!$existingChat->unlocked){
            //     return response()->json(['formError' => 'locked chat'], 422);
            // }
            // if($existingChat->created_at > $this->dayIntervalOutput){
            //     return response()->json(['formError' => 'too early'], 422);
            // }
        }
        else{
            return response()->json(['formError' => ['Bad Role']], 422);
        }

        //for KING, at least 3 days of chat.
        //for HOSTESS, if other has reviewed.
        // dd($existingChat);

        $review = Review::create([
            'reviewer_id' => $request->user()->id,
            'reviewed_user_id' => $request->reviewed_user_id,
            'rating' => ($request->rating < $leastrating) ? $leastrating : $request->rating,
            'comment' => $request->comment,
        ]);

        event(new ReviewRecieved($review));

        return response()->json(['data' => $review,'message' => "Review successfully submitted"], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $currentUserId = $user->id;

        // $reviews = User::with('chats')
        // withWhereHas('chatsWithCurrentUser', function($query) use ($currentUserId) {
        //     $query->where('user1_id', $currentUserId)
        //           ->orWhere('user2_id', $currentUserId);
        // })
        // ->where(function($query) use ($currentUserId) {
        //     $query->whereHas('chatsWithCurrentUser', function($q) use ($currentUserId) {
        //         $q->select(DB::raw('DATEDIFF(MAX(created_at), MIN(created_at)) as days'))
        //           ->having('days', '>=', 0); // 3 days means at least 2 full days between first and last message
        //     });
        // })
        // ->whereDoesntHave('reviewsReceived', function($query) use ($currentUserId) {
        //     $query->where('reviewer_id', $currentUserId);
        // })
        // ->where('id', '!=', $currentUserId)
        // ->hasProfilePicture()
        // ->forOppositeRole($request->user()->role)
        //     ->latest()
        //     ->
        //     get();

        if($user->role == User::ROLE_HOSTESS){

            //Target must have reviewed user
            // $hasReviewed = 
            // Review::select('reviewer_id','created_at')->where('reviewed_user_id',$currentUserId)->get();
            // $otherUserIds = collect($hasReviewed)->pluck('reviewer_id')->toArray();
            
            
            $reviews = User::

            //Valid Users
            hasProfilePicture()
            ->NotBanned()
            ->NotShadowBanned()
            ->forOppositeRole($user->role)

            ->whereHas('reviewsGiven', function($query) use ($currentUserId) {
                // Users who have reviewed the current user
                $query->where('reviewed_user_id', $currentUserId);
            })
            ->whereDoesntHave('reviewsReceived', function($query) use ($currentUserId) {
                // User I haven't reviewed yet
                    $query->where('reviewer_id', $currentUserId);
                })
            ->latest()
            ->get()
            ->map(function($review)  {
                $review->unlocked_at = $review->created_at;
                return $review;
                });

        }
        elseif($user->role == User::ROLE_KING){

            $chats = Chat::
                        SelectRaw("
                                    CASE 
                                        WHEN user1_id = ? THEN user2_id 
                                        ELSE user1_id 
                                    END AS other_user,
                                    DATE_ADD(unlocked_at, INTERVAL {$this->dayInterval} DAY) AS unlocked_at"
                                    ,[$user->id])
                                    
                        //->addSelect('chats.created_at')
                        ->myChats($user->id)
                        ->unlockedForDays($this->dayIntervalOutput)
                        ->get();
                        $otherUserIds = collect($chats)->pluck('other_user')->toArray();
            //return $chats;

            // $chats = Chat::
            // selectRaw("
            //             CASE 
            //                 WHEN user1_id = ? THEN user2_id 
            //                 ELSE user1_id 
            //             END AS other_user,
            //             DATE_ADD(created_at, INTERVAL {$this->dayInterval} DAY) AS unlocked_at
            //         ", [$currentUserId])
            // ->where('unlocked', 1)
            // ->where(function($q) use ($currentUserId) {
            //     $q->where('user1_id', $currentUserId)
            //     ->orWhere('user2_id', $currentUserId);
            // })
            // // ->where('user1_id', $user->id)
            // // ->orWhere('user2_id', $user->id)
            // ->where('created_at', '<=', $this->dayIntervalOutput)
            // ->get();
            // $otherUserIds = collect($chats)->pluck('other_user')->toArray();
            // return response()->json([compact('chats','otherUserIds')]);
            // dd($chats);

            $reviews = User::whereIn('id',$otherUserIds)
            ->hasProfilePicture()
            ->NotBanned()
            ->NotShadowBanned()
            ->forOppositeRole($user->role)
            ->whereDoesntHave('reviewsReceived', function($query) use ($currentUserId) {
                    $query->where('reviewer_id', $currentUserId);
                })
            ->latest()
            ->get()->map(function($review) use ($chats) {
                $chat = $chats->firstWhere('other_user', $review->id); // find matching chat
                if ($chat) {
                    $review->unlocked_at = $chat->unlocked_at;
                }
                return $review;
                });
        }
        else{
            return response()->json(['formError' => 'Bad Role'], 422);
        }
        
        

        return response()->json($reviews);
    }

    
}