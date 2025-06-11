<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Review;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $users = User::with(['profile' => function ($query) {
    //         $query->select('user_id','top_profile','verified_profile','credits'); // select only needed columns
    //     }])
    //     ->select('id', 'name', 'role', 'created_at')
    //     ->whereIn('role', [User::ROLE_KING, User::ROLE_HOSTESS])
    //     ->get();

    //     return view('home',compact('users'));
    // }
    public function index(Request $request)
    {
        $query = User::with(['profile' => function($query) {
                $query->select('user_id', 'top_profile', 'verified_profile', 'credits');
            }])
            ->select('id', 'name', 'role', 'created_at')
            ->forRoleAny()
            ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                //$q->where('id', 'like', "%{$searchTerm}%")
                $q->where('name', 'like', "%{$searchTerm}%")
                //->orWhere('role', 'like', "%{$searchTerm}%")
                // ->orWhereHas('profile', function($profileQuery) use ($searchTerm) {
                //     $profileQuery->where('top_profile', 'like', "%{$searchTerm}%")
                //                 ->orWhere('verified_profile', 'like', "%{$searchTerm}%");
                //                 // Note: Searching numeric 'credits' field as string for simplicity
                //                 // For exact number matching, use where('credits', $searchTerm)
                // })
                ;
            });
        }

        // // Optional role filter
        // if ($request->filled('role')) {
        //     $query->where('role', $request->role);
        // }

        // // Optional profile status filters
        // if ($request->filled('top_profile')) {
        //     $query->whereHas('profile', function($q) use ($request) {
        //         $q->where('top_profile', $request->top_profile);
        //     });
        // }
        
        // if ($request->filled('verified_profile')) {
        //     $query->whereHas('profile', function($q) use ($request) {
        //         $q->where('verified_profile', $request->verified_profile);
        //     });
        // }

        $users = $query->paginate(10)
            ->appends($request->except('page'));

        return view('home', compact('users'));
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

    public function toggleVerified(User $user)
    {
        $prof = UserProfile::where('user_id',$user->id)->first();
        // dd($user,$prof);
        $msg = $prof->verified_profile ? 'This user is no longer Verified' : 'This user is now Verified';
        $prof->update([
            'verified_profile' => !$prof->verified_profile
        ]);

        AuditAdmin::audit("HomeController@toggleVerified");

        return redirect()->route('user-profile', $user->name)
            ->with('success', $msg);
    }

    public function toggleTop(User $user)
    {
        $prof = UserProfile::where('user_id',$user->id)->first();
        // dd($user,$prof);
        $msg = $prof->top_profile ? 'This user is no longer Top Profile' : 'This user is now Top Profile';
        $prof->update([
            'top_profile' => !$prof->top_profile
        ]);
        AuditAdmin::audit("HomeController@toggleTop");
        return redirect()->route('user-profile', $user->name)
            ->with('success', $msg);
    }

    public function editUserPassword($id)
    {
        $user = User::where('id','=', $id)
        ->forRoleAny()
        ->first();
        if(!$user){
            abort(404);
        }
        return view('user-profile.user-password',compact('user'));
    }

    public function updateUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            //'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        
        //dd($validator);
        $user = User::where('id','=', $request->user_id)
        ->forRoleAny()
        ->first();
        if(!$user){
            abort(404);
        }

        // $validator->after(function ($validator) use ($request) {
        //     if (!Hash::check($request->current_password, auth()->user()->password)) {
        //         $validator->errors()->add('current_password', 'Your current password is incorrect.');
        //     }
        // });
        //dd($validator);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        
        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        AuditAdmin::audit("HomeController@updateUserPassword");


        return redirect()->route('user-profile.password.edit', $user->id)
            ->with('success', "$user->name's Password changed successfully!");
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
