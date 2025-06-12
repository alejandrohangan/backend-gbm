<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('online', function($user)  {
    return $user;
});

Broadcast::channel('notifications.tickets', function ($user) {
    return $user->hasRole(['agent', 'admin']);
});