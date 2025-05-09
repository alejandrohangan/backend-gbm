<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Http\Request;

class SentMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $receiver = User::find($request->receiver);
        $sender = User::find($request->sender);

        broadcast(new MessageSent($sender, $receiver, $request->message));
        
        return response()->noContent();
    }
}