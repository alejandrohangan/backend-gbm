<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationSentController extends Controller
{
    public function sendNotificationConfirmation(Request $request) {
        $sender = User::find($request->sender);
        $senderName = $sender->name;

        broadcast(New NotificationSent($sender,"$senderName creó un nuevo ticket"))->toOthers();

        return response()->noContent();
    }
}