<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications', function () {
    return true;
});

Broadcast::channel('private-stagiaire.{stagiaireId}', function ($user, $stagiaireId) {
    $user->loadMissing('stagiaire');
    return $user->stagiaire && $user->stagiaire->id == $stagiaireId;
});
