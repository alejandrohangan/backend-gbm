<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    public function getConversations()
    {
        $authUserId = Auth::id();

        $conversations = Conversation::where('user_id1', $authUserId)
            ->orWhere('user_id2', $authUserId)
            ->with(['user1', 'user2'])
            ->get();

        return response()->json($conversations);
    }

    public function store(Request $request, int $id) {
        $sender = Auth::user();
        $receiver = User::findOrfail($id);
        $message = new MessageSent($sender, $receiver, $request->message);
    }

    public function getMessages(int $id)
    {
        $user = User::findOrfail($id);
        $authUserId = Auth::id();
        $otherUserId = $user->id;

        $messages = Message::query()
            ->where(function ($query) use ($authUserId, $otherUserId) {
                $query->where('sender_id', $authUserId)
                    ->where('recipient_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($authUserId, $otherUserId) {
                $query->where('sender_id', $otherUserId)
                    ->where('recipient_id', $authUserId);
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }
}
