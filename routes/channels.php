<?php

use App\Channel;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('Channel-{id}', function ($user, $id) {
    $channel = Channel::find($id);
    if ($channel->confidentiality == 1) {
        return true;
    } else {
        return $user->allChannels->contains($id);
    }
});

Broadcast::channel('Instance-{id}', function ($user, $id) {
    return $user->instances->contains($id);
});


Broadcast::channel('livestage-{channelId}', function ($user, $channelId) {
    if ($user->canJoinStage($channelId)) {
        return ['id' => $user->id, 'name' => $user->getNameDisplay()];
    }
    return false;
});

Broadcast::channel('collab-service.{docId}', function ($user, $docId) {
    return true;
});
