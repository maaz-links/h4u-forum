<?php

// app/Http/Controllers/ChatController.php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
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

        return $this->buyChat->buychat($user,$request->other_user_id,config('h4u.chatcost.standard',10));

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

                $newChat = Chat::create([
                    'user1_id' => $king_id,
                    'user2_id' => $user->id,
                    'unlocked' => 0,
                ]);
        
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
        
        $chats = Chat::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->when($type === 'archived', function($query) {
                $query->where('is_archived', true);
            }, function($query) {
                $query->where('is_archived', false);
            })
            ->with([
                //'user1', 'user2',
                
                'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function($chat) use ($user) {
                $chat->other_user = $chat->user1_id === $user->id ? $chat->user2 : $chat->user1;
                $chat->other_user->province_name = $chat->other_user->profile->province_name;
                // $chat->other_user = $chat->otherUser();
                $chat->last_message = $chat->messages->first();

                //unset($chat->user1, $chat->user2, $chat->other_user->profile); //Remove form list after query
                return $chat;
            });
            
        return response()->json($chats);
    }

    public function show(Chat $chat)
    {
        //$this->authorize('view', $chat);
        
        $chat->load(['user1', 'user2']);
        $chat->other_user = $chat->user1_id === auth()->id() ? $chat->user2 : $chat->user1;
        $chat->other_user->province_name = $chat->other_user->profile->province_name;
        unset($chat->user1, $chat->user2, $chat->other_user->profile, $chat->messages); //Remove form list after query
        return response()->json($chat);
    }

    public function archive(Chat $chat)
    {
        //$this->authorize('update', $chat);
        
        $chat->update(['is_archived' => true]);
        return response()->json(['message' => 'Chat archived']);
    }

    public function unarchive(Chat $chat)
    {
        //$this->authorize('update', $chat);
        
        $chat->update(['is_archived' => false]);
        return response()->json(['message' => 'Chat unarchived']);
    }
}