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
        $users = User::where('id', '!=', Auth::id())->get();
        return response()->json($users);
    }

    public function store(Request $request, int $id)
    {
        $sender = Auth::user();
        $receiver = User::findOrfail($id);

        // Buscar o crear una conversación entre estos usuarios
        $conversation = Conversation::where(function ($query) use ($sender, $receiver) {
            $query->where('user_id1', $sender->id)
                ->where('user_id2', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('user_id1', $receiver->id)
                ->where('user_id2', $sender->id);
        })->first();

        // Si no existe una conversación, la creamos
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id1' => $sender->id,
                'user_id2' => $receiver->id
            ]);
        }

        // Guardar el mensaje en la base de datos
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

    public function getMessages(int $id)
    {
        $user = User::findOrfail($id);
        $authUserId = Auth::id();
        $otherUserId = $user->id;

        $messages = Message::query()
            ->where(function ($query) use ($authUserId, $otherUserId) {
                $query->where(function ($q) use ($authUserId, $otherUserId) {
                    $q->where('sender_id', $authUserId)
                        ->where('receiver_id', $otherUserId);
                })
                    ->orWhere(function ($q) use ($authUserId, $otherUserId) {
                        $q->where('sender_id', $otherUserId)
                            ->where('receiver_id', $authUserId);
                    });
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }
}
