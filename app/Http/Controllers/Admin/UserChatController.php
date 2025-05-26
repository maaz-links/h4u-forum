<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class UserChatController extends Controller
{
    public function index(Request $request){
        $chats = Chat::with([
            'user1' => function($query)  {
                $query->select('id','name');
            },
            'user2' => function($query)  {
                $query->select('id','name');
            }
        
        ])->get();

        return view('chats.index',compact('chats'));
    }
    public function userchats(string $name){
        $user = User::select('id','name','role')->forUsername($name)->forRoleAny()->first();
        if(!$user){
            abort(404);
        }
        $chats = Chat::
        
            whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->
            with([
                'participants' => function($query) use($user) {
                $query->select('id','user_id','name');
                },
            ])->
            where('unlocked',1)->
            get()->map(function($chat) use ($user) {
                //$chat->other_user = $chat->user2_id === $user->id ? $chat->user1 : $chat->user2;
                $other_user_index = $chat->participants[0]->id === $user->id ? 1 : 0;
                //$chat->other_user = $chat->participants[0]->id === $user->id ? $chat->participants[0] : $chat->participants[1];
                $chat->my_user_id = $user->id;
                $chat->other_user = $chat->participants[$other_user_index];

                unset($chat->user1, $chat->user2, $chat->other_user->pivot,$chat->participants); //Remove form list after query
                return $chat;
            });
            // return $chats;
            return view('user-profile.chat',compact('chats','name'));
    }

    public function conversation(Request $request ,Chat $chat){
        // $validated_data = $request->validate([
        //     'chat_id' => 'required|numeric|exists:chats,id',
        //     // 'admin_reason' => 'required|string',
        // ]);

        

        //return view('user-profile.chat',compact('chats'));
        // $chat = Chat::where('id', $validated_data['chat_id'])
        // ->with([
         $chat->load([
            'user1' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
            'user2' => function($query) {
                $query->select('id','name','role','profile_picture_id');
            },
            'messages' => function($query) {
                $query->with([
                    'sender' => function($query) {
                        $query->select('id','name','role','profile_picture_id');
                    },            
            ]);
            },
        ])
        ->first();

        AuditAdmin::audit("UserChatController@openConversation");

        return view('chats.openconversation',compact('chat'));
    }

    public function editMsg(Chat $chat,Message $msg){

        //return $msg;
        return view('chats.messages.edit',compact('msg'));
    }

    public function updateMsg(Request $request,Message $msg){
        $validated = $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        $msg->update($validated);

        AuditAdmin::audit("UserChatController@editMessage");


        return redirect()->back()
            ->with('success', 'Message updated successfully');
    }

    public function destroyMsg(Message $msg)
    {
        //dd($msg);
        $chat_id = $msg->chat_id;
        $msg->delete();

        AuditAdmin::audit("UserChatController@destroyMessage");

        return redirect()->route('open.conversation',['chat' => $chat_id])
            ->with('success', 'Message deleted successfully');
    }

}
