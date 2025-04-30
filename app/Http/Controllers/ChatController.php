<?php

// app/Http/Controllers/ChatController.php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\BuyChat;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $buyChat;

    public function __construct(BuyChat $buyChat)
    {
        $this->buyChat = $buyChat;
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

        //dd($profile);
        //return $profile;
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
        return $this->buyChat->buychat($user,$request->other_user_id,$cost);

    }

    public function freeChat(Request $request){
        $user = $request->user();

        $existingChat = Chat::findBetweenUsers($user->id, $request->other_user_id);
        
        if($existingChat){
            return response()->json(['message' => 'Chat already exists'],200);
        }

        $profile = DB::table('user_profiles')->where('user_id', $user->id)->first();

        if (!$profile || $profile->credits < 1) {
            return response()->json(['message' => 'You have reached the limit of free messages today'], 400);
        }
        $king_id = $request->other_user_id;
        return DB::transaction(function () use ($user, $king_id) {

            DB::table('user_profiles')
                ->where('user_id', $user->id)
                ->decrement('credits', 1);

                // $newChat = Chat::create([
                //     'user1_id' => $king_id,
                //     'user2_id' => $user->id,
                //     'unlocked' => 0,
                // ]);
                $newChat = $this->buyChat->fullChatInstance($king_id,$user->id);
        
                $newChat->messages()->create([
                    'sender_id' => $user->id,
                    'message' => "Hi! Nice to meet you, wanna chat?",
                ]);
        
                return response()->json(['message' => 'Message Given','chat' => $newChat]);
        });
        
        // $newChat = Chat::create([
        //     'user1_id' => $user->id,
        //     'user2_id' => $request->king_id,
        //     'unlocked' => 0,
        // ]);

        // $newChat->messages()->create([
        //     'sender_id' => $user->id,
        //     'message' => "Hi! Nice to meet you, wanna chat?",
        // ]);

        // return response()->json(['message' => 'Message Given','chat' => $newChat]);

        //return $this->buyChat->buychat($user,$request->hostess_id,config('h4u.chatcost.standard',10));

     
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');
        
        $chats = Chat::
        // where(function($query) use ($user) {
        //         $query->where('user1_id', $user->id)
        //               ->orWhere('user2_id', $user->id);
        //     })
            myChats($user->id)
            //->addSelect(['my_user' => Chat::select('name')])
            // ->when($type === 'archived', function($query) {
            //     $query->where('is_archived', true);
            // }, function($query) {
            //     $query->where('is_archived', false);
            // })
            ->whereHas('participants', function($query) use ($user, $type) {
                $query->where('user_id', $user->id)
                      ->where('is_archived', $type === 'archived');
            })
            ->with([
                'user1', 'user2',
                // 'user1' => function($q) 
                // { $q->with('profile')->select('user.profiles.province_name'); },
                //'participants',
                'participants' => function($query) use($user) {
                $query->select('id')->where('id',$user->id);
                },
                'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            // ->addSelect(chat)
            // ->leftJoin('chat_user','chat_user.chat_id','=','chat.id')
            ->get()
            // ;
            ->map(function($chat) use ($user) {
                $chat->other_user = $chat->user2_id === $user->id ? $chat->user1 : $chat->user2;
                $chat->is_archived = $chat->participants[0]->pivot->is_archived;
                $chat->archived_at = $chat->participants[0]->pivot->archived_at;
                $chat->other_user->province_name = $chat->other_user->profile->province_name;
                // $chat->other_user = $chat->otherUser();
                $chat->last_message = $chat->messages->first();

                unset($chat->user1, $chat->user2, $chat->other_user->profile,$chat->participants); //Remove form list after query
                return $chat;
            });
            
        return response()->json($chats);
    }

    public function show(Request $request,Chat $chat)
    {
        //$this->authorize('view', $chat);
        $user= $request->user();
        $chat->load(['user1', 'user2',
        'participants' => function($query) use($user) {
                $query->select('id')->where('id',$user->id);
                },
        ]);
        $chat->other_user = $chat->user1_id === auth()->id() ? $chat->user2 : $chat->user1;
        $chat->other_user->province_name = $chat->other_user->profile->province_name;
        $chat->is_archived = $chat->participants[0]->pivot->is_archived;
        $chat->archived_at = $chat->participants[0]->pivot->archived_at;
        unset($chat->user1, $chat->user2, $chat->other_user->profile, $chat->messages,$chat->participants); //Remove form list after query
        return response()->json($chat);
    }

    public function archive(Request $request,Chat $chat)
    {
        $user = $request->user();
        //$this->authorize('update', $chat);
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
        //$this->authorize('update', $chat);
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