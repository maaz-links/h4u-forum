<?php

// app/Http/Controllers/ChatController.php
namespace App\Http\Controllers;

use App\Events\FreeMessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\BuyChat;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $createChatService;

    public function __construct(BuyChat $createChatService)
    {
        $this->createChatService = $createChatService;
    }

    public function create(Request $request){
        $user = $request->user();
      
        if($user->role == User::ROLE_KING){
            return $this->createChat($request);
        }
        elseif($user->role == User::ROLE_HOSTESS){
            return $this->freeChat($request);
        }
        else{
            return response()->json(['message' => 'Bad Action'],401);
        }
    }

    public function createChat(Request $request){
        $user = $request->user();

        $profile = DB::table('user_profiles')->select('top_profile','verified_profile')->where('user_id',$request->other_user_id)->first();

        $cost = config('h4u.chatcost.standard');
        if($profile->verified_profile){
            $cost = config('h4u.chatcost.verified');
        }
        if($profile->top_profile){
            $cost = config('h4u.chatcost.topprofile');
        }
        if($profile->top_profile && $profile->verified_profile){
            $cost = config('h4u.chatcost.verified_topprofile');
        }
        //return $cost;
        return $this->createChatService->buychat($user,$request->other_user_id,$cost);

    }

    public function freeChat(Request $request){
        $user = $request->user();

        return $this->createChatService->freechat($user,$request->other_user_id);
        // $existingChat = Chat::findBetweenUsers($user->id, $request->other_user_id);
        
        // if($existingChat){
        //     return response()->json(['message' => 'Chat already exists'],200);
        // }

        // $profile = DB::table('user_profiles')->where('user_id', $user->id)->first();

        // if (!$profile || $profile->credits < 1) {
        //     return response()->json(['message' => 'You have reached the limit of free messages today'], 400);
        // }
        // $king_id = $request->other_user_id;
        // return DB::transaction(function () use ($user, $king_id) {

        //     DB::table('user_profiles')
        //         ->where('user_id', $user->id)
        //         ->decrement('credits', 1);

        //         $newChat = $this->createChatService->fullChatInstance($king_id,$user->id);
        
        //         $newChat->messages()->create([
        //             'sender_id' => $user->id,
        //             'message' => "Hi! Nice to meet you, wanna chat?",
        //         ]);

        //         event(new FreeMessageSent($user->id, $king_id));
        
        //         return response()->json(['message' => 'Message Given','chat' => $newChat]);
        // });
     
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');
        
        $chats = Chat::
       //Get participants of the chat, we need archive status from current user and full data of other user.
       
            whereHas('participants', function($query) use ($user, $type) {

                //Chat belongs to current user if chat->participant->user_id == currentUserId
                $query->where('user_id', $user->id)
                      ->where('is_archived', $type === 'archived');
            })
            ->
                //Exclude chats with banned users
                nonBannedChats()
            ->
            with([
              
                'participants' => function($query) use($user) {
               
                    //We need these extra attributes from UserProfile (seperate table)
                $query->with(['profile' => function($query) use($user) {
                    $query->select('id', 'user_id','province_id','top_profile','verified_profile');
                }
                    
                ]);
                },
               
                //Get last message content
                'messages' => function($query) {
                $query->latest()->limit(1);
            }
            ])
    
            //Get created_at of last message for sorting.
            // If a chat has no messages then use created_at attribute of the chat itself for sorting
            ->addSelect([
                'latest_message_created_at' => Message::select('created_at')
                    ->whereColumn('chat_id', 'chats.id')
                    ->latest()
                    ->limit(1)
            ])
            ->orderByDesc(DB::raw('COALESCE(latest_message_created_at, chats.created_at)'))

            ->get()
            // ;
            //Map these values properly if you want to work with frontend.
            ->map(function($chat) use ($user) {
                
                //Get index of other user in participants relation. There must be two users in a chat as participants.
                $other_user_index = $chat->participants[0]->id === $user->id ? 1 : 0;

                $chat->other_user = $chat->participants[$other_user_index];
                $chat->is_archived = $chat->participants[!$other_user_index]->pivot->is_archived;
                $chat->archived_at = $chat->participants[!$other_user_index]->pivot->archived_at;
                $chat->other_user->province_id = $chat->other_user->profile->province_id;
                $chat->other_user->verified_profile = $chat->other_user->profile->verified_profile;
                $chat->other_user->top_profile = $chat->other_user->profile->top_profile;

                $chat->last_message = $chat->messages->first();


                unset($chat->user1, $chat->user2, $chat->other_user->profile,$chat->participants); //Remove from list after query
                return $chat;
            });
            
        return response()->json($chats);
    }


    public function archive(Request $request,Chat $chat)
    {
        $user = $request->user();
        if (!$chat->hasParticipant($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $chat->participants()->updateExistingPivot($user->id, [
            'is_archived' => true,
            'archived_at' => now()
        ]);
        //$chat->update(['is_archived' => true]);
        return response()->json(['message' => 'Chat archived']);
    }

    public function unarchive(Request $request,Chat $chat)
    {
        $user = $request->user();
        if (!$chat->hasParticipant($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $chat->participants()->updateExistingPivot($user->id, [
            'is_archived' => false,
            'archived_at' => null
        ]);
        
        $chat->update(['is_archived' => false]);
        return response()->json(['message' => 'Chat unarchived']);
    }
}