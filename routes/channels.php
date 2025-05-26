<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat', function ($user) {
    return true; // Or use your custom logic to check if user can join
});

Broadcast::channel('chatter.{chatId}', function ($user, $chatId) {
    // Check if the user is part of the chat
    $check =  \App\Models\Chat::where('id', $chatId)
        ->where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })->exists();
        // Log::info('info:'.$check);
        // Log::info('info:true');
        return $check;
    // return true;
});

// Broadcast::channel('myChannel.{userId}', function ($user, $userId) {
//     return $user->id === $userId;
// });