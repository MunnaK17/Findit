<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin', function (User $user) {
    return $user->isAdmin();
});

Broadcast::channel('user.{id}', function (User $user, int $id) {
    return $user->id === $id;
});
