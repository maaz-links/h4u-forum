<?php

// app/Http/Controllers/MessageController.php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController2 extends Controller
{
    // public function index(Chat $chat)
    // {
    //     //$this->authorize('view', $chat);
        
    //     $messages = $chat->messages()
    //         // ->with('sender')
    //         ->orderBy('created_at', 'asc')
    //         ->get()
    //         ->map(function($message) {
    //             return [
    //                 'id' => $message->id,
    //                 'text' => $message->message,
    //                 'time' => $message->created_at->format('H:i'),
    //                 'sent' => $message->sender_id === auth()->id(),
    //                 // 'sender_name' => $message->sender->name,
    //             ];
    //         })
    //         ;
            
    //     return response()->json($messages);
    // }
    public function index(Chat $chat)
{
    $currentUserId = auth()->id();
    
    // Get all messages ordered by creation time
    $messages = $chat->messages()
        ->orderBy('created_at', 'asc')
        ->get();
    
    // Find the last unread message not sent by current user
    $lastUnreadMessage = $messages
        ->where('sender_id', '!=', $currentUserId)
        ->where('is_read', 0)
        ->last();
    
    // Mark it as read if found
    if ($lastUnreadMessage) {
        $lastUnreadMessage->is_read = 1;
        $lastUnreadMessage->save();
    }
    
    // Transform the messages for response
    $response = $messages->map(function($message) use ($currentUserId) {
        return [
            'id' => $message->id,
            'text' => $message->message,
            'time' => $message->created_at->format('H:i'),
            'sent' => $message->sender_id === $currentUserId,
            'is_read' => $message->is_read,
        ];
    });
        
    return response()->json($response);
}

    public function store(Request $request, Chat $chat)
    {
        //$this->authorize('view', $chat);
        
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $message = $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        return response()->json([
            'id' => $message->id,
            'text' => $message->message,
            'time' => $message->created_at->format('H:i'),
            'sent' => true,
            'is_read' => false,
            // 'sender_name' => $message->sender->name,
        ]);
    }

    // public function poll(Chat $chat)
    // {
    //     $lastMessageId = request()->query('last_message_id', 0);
    //     $currentUserId = auth()->id();
        
    //     // Get new messages not sent by current user
    //     $messages = $chat->messages()
    //         ->where('id', '>=', $lastMessageId)
    //         ->where('sender_id', '!=', $currentUserId)
    //         ->orderBy('created_at', 'asc')
    //         ->get();
        
    //     $specificMessage = $messages->firstWhere('id', $lastMessageId);
    //     $messages = $messages->where('id', '>', $lastMessageId);

    //     $read_trigger = 0;
    //     if($specificMessage){
    //         if($specificMessage->is_read){
    //             $read_trigger = 1;
    //         }
    //     }

    //     // Mark last unread messages as read.
    //     if ($messages->isNotEmpty()) {
    //         $messages->last()->update(['is_read' => 1]);
    //     }
        
    //     // Transform for response
    //     $response = $messages->map(function($message) {
    //         return [
    //             'id' => $message->id,
    //             'text' => $message->message,
    //             'time' => $message->created_at->format('H:i'),
    //             'sent' => false,
    //             'is_read' => $message->is_read,
    //             //'is_read' => false,
    //         ];
    //     });
            
    //     return response()->json(['messages'=>$response,'is_read'=>$read_trigger]);
    // }
    public function poll(Chat $chat)
    {
        //$this->authorize('view', $chat);
        
        $lastMessageId = request()->query('last_message_id', 0);
        
        $messages = $chat->messages()
            ->where('id', '>', $lastMessageId)
            ->where('sender_id', '!=', auth()->id())
            // ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'text' => $message->message,
                    'time' => $message->created_at->format('H:i'),
                    'sent' => false,
                    // 'sender_name' => $message->sender->name,
                ];
            });
            
        return response()->json($messages);
    }
    public function pollread(Chat $chat)
    {
        //$this->authorize('view', $chat);
        
        $lastMessageId = request()->query('last_message_id', 0);
        
        $message = $chat->messages()
            ->where('id', '=', $lastMessageId)
            ->first();
        
        if($message->sender_id != auth()->id()) {
            $message->update(['is_read'=> 1]);
        }else{
            return response()->json(['is_read' => $message->is_read]);
        }
            
        return response()->json($message);
    }
}