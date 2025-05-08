<?php

// app/Http/Controllers/MessageController.php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Chat $chat)
    {
        //$this->authorize('view', $chat);
        
        $messages = $chat->messages()
            // ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'text' => $message->message,
                    'time' => $message->created_at->format('H:i'),
                    'sent' => $message->sender_id === auth()->id(),
                    // 'sender_name' => $message->sender->name,
                ];
            })
            ;
            
        return response()->json($messages);
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
            // 'sender_name' => $message->sender->name,
        ]);
    }

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
}