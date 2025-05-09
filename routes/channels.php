<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications.staff', function($user)  {
    return in_array($user->rol, ['admin', 'agent']);
});