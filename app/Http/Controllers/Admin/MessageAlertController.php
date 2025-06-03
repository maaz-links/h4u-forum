<?php

namespace App\Http\Controllers\Admin;

use App\Events\Chat\NewMessageSent;
use App\Events\Chat\NewMessageSentAfterMod;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\MessageAlert;
use App\Services\AuditAdmin;
use Illuminate\Http\Request;

class MessageAlertController extends Controller
{
    public function index()
    {
        $alerts = MessageAlert::with(['user', 'chat'])
            ->orderBy('message_created_at', 'desc')
            ->paginate(10);
            
        return view('admin.message-alerts.index', compact('alerts'));
    }

    public function show(MessageAlert $alert)
    {
        return view('admin.message-alerts.show', compact('alert'));
    }

    public function update(Request $request, MessageAlert $alert)
    {
        if($alert->isFinalized()){
            return redirect()->back()->with('success', 'Cannot change status of approved or rejected alerts.');
        }
        $validated = $request->validate([
            'status' => 'required|in:APPROVED,REJECTED,ARCHIVED',
            'notes' => 'nullable|string',
        ]);

        if($request->status == "APPROVED"){
            $this->insertMessage($alert);
        }
        
        $alert->update([
            'status' => $validated['status'],
            //'admin_notes' => $validated['notes'] ?? null,
        ]);
        
        
        AuditAdmin::audit("MessageAlertController@update?".$request->status);
        
        return redirect()->back()->with('success', 'Alert status updated successfully');
    }

    protected function insertMessage($alert){
        $chat = Chat::where('id', $alert->chat_id)->first();
        $message = $chat->messages()->create([
            'sender_id' => $alert->user_id,
            'message' => $alert->message_body,
            'created_at' => $alert->message_created_at,
            'updated_at' => $alert->message_created_at,
            'is_read' => 0,
        ]);
        event(new NewMessageSentAfterMod($message, $chat));
        //$messageFormatted = new MessageResource($message);
        //$messageFormatted->sent = false;
        //dd($messageFormatted->sent);
        //event(new NewMessageSent($message, $chat));
    }
}