<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{

    public function getConversations()
    {
        $authUserId = Auth::id();

        $conversations = DB::table('conversations')
            ->leftJoin('users as u1', 'conversations.user_id1', '=', 'u1.id')
            ->leftJoin('users as u2', 'conversations.user_id2', '=', 'u2.id')
            ->select(
                'conversations.*',
                DB::raw('CASE 
                WHEN conversations.user_id1 = ' . $authUserId . ' THEN u2.name 
                ELSE u1.name 
            END as other_user_name'),
                DB::raw('CASE 
                WHEN conversations.user_id1 = ' . $authUserId . ' THEN u2.id 
                ELSE u1.id 
            END as other_user_id')
            )
            ->where(function ($query) use ($authUserId) {
                $query->where('user_id1', $authUserId)
                    ->orWhere('user_id2', $authUserId);
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($conversations);
    }

    public function store(Request $request, int $conversationId)
    {
        $sender = Auth::user();
        $authUserId = Auth::id();

        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($query) use ($authUserId) {
                $query->where('user_id1', $authUserId)
                    ->orWhere('user_id2', $authUserId);
            })
            ->firstOrFail();

        $receiverId = ($conversation->user_id1 == $authUserId)
            ? $conversation->user_id2
            : $conversation->user_id1;

        $receiver = User::findOrFail($receiverId);

        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'conversation_id' => $conversation->id,
            'message' => $request->message
        ]);

        // Transmitir el mensaje por broadcast
        $message = new MessageSent($sender, $receiver, $request->message);
        broadcast($message);

        return response()->json(['status' => 'Message sent successfully']);
    }

    public function getMessages(int $conversationId)
    {
        $authUserId = Auth::id();

        // Verificar que el usuario pertenece a esta conversación
        $conversation = DB::table('conversations')
            ->where('id', $conversationId)
            ->where(function ($query) use ($authUserId) {
                $query->where('user_id1', $authUserId)
                    ->orWhere('user_id2', $authUserId);
            })
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversación no encontrada'], 404);
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }
}
