<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class BuyChat
{

    public function buychat(User $user, $other_user_id, $amount)
    {
        $userA = $user->id;
        $userB = $other_user_id;

        // $existingChat = Chat::where(function ($query) use ($userA, $userB) {
        //     $query->where('user1_id', $userA)
        //           ->where('user2_id', $userB);
        // })->orWhere(function ($query) use ($userA, $userB) {
        //     $query->where('user1_id', $userB)
        //           ->where('user2_id', $userA);
        // })->first();
        $existingChat = Chat::findBetweenUsers($user->id,$other_user_id);
        
        if($existingChat){
            if($existingChat->unlocked){
                return response()->json(['message' => 'Chat already exists'],200);
            }
        }

        $profile = DB::table('user_profiles')->where('user_id', $user->id)->first();

        if (!$profile || $profile->credits < $amount) {
            return response()->json(['message' => 'Insufficient credits','shop_redirect' => true], 400);
        }

        return DB::transaction(function () use ($user, $other_user_id,$amount,$existingChat) {

            
            DB::table('user_profiles')
                ->where('user_id', $user->id)
                ->decrement('credits', $amount);

            if($existingChat){
                $newChat = $existingChat->update(['unlocked' => 1]);
            }
            else{
                $newChat = Chat::create([
                    'user1_id' => $user->id,
                    'user2_id' => $other_user_id,
                    'unlocked' => 1,
                ]);
            }           
            return response()->json(['message' => 'Chat Created','chat' => $newChat]);
        });
    }

}