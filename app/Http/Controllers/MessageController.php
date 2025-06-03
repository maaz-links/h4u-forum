<?php

// app/Http/Controllers/MessageController.php
namespace App\Http\Controllers;

use App\Events\Chat\MarkMessageRead;
use App\Events\Chat\NewMessageSent;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageAlert;
use App\Services\MessageModerator;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Notification;

class MessageController extends Controller
{
    public function index(Chat $chat)
    {
        //$this->authorize('view', $chat);
        if(!$this->authorizeChat($chat)){
            return response()->json(["message"=> "Unauthorized"],401);
        };
        
        $messages = $chat->messages()
            // ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            // ->map(function($message) {
            //     // return [
            //     //     'id' => $message->id,
            //     //     'text' => $message->message,
            //     //     'time' => $message->created_at,
            //     //     'sent' => $message->sender_id === auth()->id(),
            //     //     'is_read' => $message->is_read
            //     //     // 'sender_name' => $message->sender->name,
            //     // ];
            //     //return new MessageResource($message);
            // })
            ;
            
        //return response()->json($messages);
        return MessageResource::collection($messages);
    }
    

    public function store(Request $request, Chat $chat)
    {
        //$this->authorize('view', $chat);
        if(!$this->authorizeChat($chat)){
            return response()->json(["message"=> "Unauthorized"],401);
        };

        if(!$chat->unlocked){
            return response()->json(["message"=> "Forbidden"],403);
        }
        
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        

        //MODERATION
        $moderator = new MessageModerator();
        $violations = $moderator->analyze($request->message);

        if (!empty($violations)) {
            $alert = MessageAlert::create([
                'user_id' => $request->user()->id,
                'chat_id' => $chat->id,
                'message_body' => $request->message,
                'message_created_at' => now(),
                'detected_rules' => $violations,
                //'status' => 'pending'
            ]);

            //SEND EMAIL TO ADMINS
            // Dispatch event/notification to recipient
            Notification::route('mail', env('SUPPORT_EMAIL_ADDRESS','support@hostessforyou.com'))
            ->notify(new \App\Notifications\MessageViolationNotif($alert));

            
            return response()->json([
                'message' => 'Your message is under review due to a potential policy violation and will be delivered upon approval.'
            ], 202);
        }

        $message = $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        // $messageFormatted = [
        //     'id' => $message->id,
        //     'text' => $message->message,
        //     'time' => $message->created_at,
        //     'sent' => true,
        //     'is_read' => $message->is_read
        //     // 'sender_name' => $message->sender->name,
        // ];
        $messageFormatted = new MessageResource($message);
        //$messageFormatted->sent = false;

        //dd($messageFormatted->sent);
        event(new NewMessageSent($message, $chat));
        // broadcast(new NewMessageSent($message,$chat->id))->toOthers();
        // event(new \App\Events\Chat\MessageSent($message));
        // return response()->json([
        //     'id' => $message->id,
        //     'text' => $message->message,
        //     'time' => $message->created_at->format('H:i'),
        //     'sent' => true,
        //     // 'sender_name' => $message->sender->name,
        // ]);
        //return response()->json($messageFormatted);
        return $messageFormatted;
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

    public function markAsRead(Request $request, Chat $chat){

        $reader = $request->user();
        $lastMessage = Message::where('chat_id', $chat->id)
        ->where('sender_id', '!=', $reader->id)
        // ->whereNull('read_at')
        ->latest()
        ->first();

        // dd($lastMessage);

        if ($lastMessage) {
            if ($lastMessage->is_read == 0) {
                $lastMessage->update(['is_read' => 1]);
                event(new MarkMessageRead($lastMessage->id, $reader->id, $chat->id));
                return response()->json(['message'=>'readset']);
            }
        }

        return response()->json(['message'=>'ok']);
    }

    protected function authorizeChat(Chat $chat){
        return $chat->hasParticipant(Auth::id());
    }

    public function unreadCount(Request $request){
        $user = $request->user();
        $count = Chat::myChats($user->id)->whereHas('messages', function ($query) use ($user) {
            // $query->select(DB::raw(1))
            //       ->whereColumn('messages.chat_id', 'chats.id')
            //       ->orderByDesc('messages.created_at')
            //       ->limit(1)
            //       ->where('is_read', 0)
            //       ->where('sender_id', '!=', $user->id);
            
                $query->where('is_read', 0) //Unread msg
                      ->where('sender_id', '!=', $user->id) //Not cureent user's message
                      //Must be the last message of the chat
                      ->whereRaw('messages.id = (
                          SELECT id FROM messages 
                          WHERE chat_id = chats.id 
                          ORDER BY created_at DESC 
                          LIMIT 1
                      )');
            
        })->count();

        return $count;
    }
}