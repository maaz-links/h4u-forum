<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::with(['profile' => function ($query) {
            $query->select('user_id','top_profile','verified_profile','credits'); // select only needed columns
        }])
        ->select('id', 'name', 'role', 'created_at')
        ->whereIn('role', [User::ROLE_KING, User::ROLE_HOSTESS])
        ->get();

        return view('home',compact('users'));
    }

    public function profile(string $name){
        $user = User::with(['profile'=> function ($query) {
        }])->forUsername($name)
        ->forRoleAny()
        ->first();
        if(!$user){
            abort(404);
        }
        $user->rating = $user->getRatingAttribute();
        $user = new UserResource($user);
        return view('user-profile.index',compact('user'));
    }

    // public function allchats(string $name){
    //     $user = User::select('id','name','role')->forUsername($name)->forRoleAny()->first();
    //     if(!$user){
    //         abort(404);
    //     }
    //     $chats = Chat::
        
    //         whereHas('participants', function($query) use ($user) {
    //             $query->where('user_id', $user->id);
    //         })
    //         ->
    //         with([
    //             'participants' => function($query) use($user) {
    //             $query->select('id','user_id','name');
    //             },
    //         ])->
    //         where('unlocked',1)->
    //         get()->map(function($chat) use ($user) {
    //             //$chat->other_user = $chat->user2_id === $user->id ? $chat->user1 : $chat->user2;
    //             $other_user_index = $chat->participants[0]->id === $user->id ? 1 : 0;
    //             //$chat->other_user = $chat->participants[0]->id === $user->id ? $chat->participants[0] : $chat->participants[1];
    //             $chat->my_user_id = $user->id;
    //             $chat->other_user = $chat->participants[$other_user_index];

    //             unset($chat->user1, $chat->user2, $chat->other_user->pivot,$chat->participants); //Remove form list after query
    //             return $chat;
    //         });
    //         // return $chats;
    //         return view('user-profile.chat',compact('chats','name'));
    // }

    // public function conversation(Request $request){
    //     $validated_data = $request->validate([
    //         'chat_id' => 'required|numeric|exists:chats,id',
    //         'admin_reason' => 'required|string',
    //     ]);

        

    //     //return view('user-profile.chat',compact('chats'));
    //     $chat = Chat::where('id', $validated_data['chat_id'])
    //     ->with([
    //         'user1' => function($query) {
    //             $query->select('id','name','role','profile_picture_id');
    //         },
    //         'user2' => function($query) {
    //             $query->select('id','name','role','profile_picture_id');
    //         },
    //         'messages' => function($query) {
    //             $query->with([
    //                 'sender' => function($query) {
    //                     $query->select('id','name','role','profile_picture_id');
    //                 },            
    //         ]);
    //         },
    //     ])
    //     ->first();

    //     AuditAdmin::audit(
    //         "Opened conversation between {$chat->user1->name} (ID: {$chat->user1->id}) and {$chat->user2->name} (ID: {$chat->user2->id})",
    //         $request->admin_reason);

    //     return view('openconversation',compact('chat'));
    // }

    
}
