<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Http\Controllers\BaseController;
use App\NewsFeed;
use App\User;
use App\Project;
use App\House;
use App\Community;
use App\Notif;
use App\Comment;
use App\NetframeAction;
use App\Interest;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CheckUserTag' => [
            'App\Listeners\UserTag',
        ],
        'App\Events\UploadMedia' => [
            'App\Listeners\ComputeMediaSize',
        ],
        'App\Events\NewPost' => [
            'App\Listeners\NewsFeedInsertUpdate',
        ],
        'App\Events\PostAction' => [
            'App\Listeners\NewsFeedUpdate',
        ],
        'App\Events\LikeElement' => [
            'App\Listeners\UpdateLikeCounter',
        ],
        'App\Events\NewComment' => [
            'App\Listeners\NotifyComment',
        ],
        'App\Events\NewAction' => [
            'App\Listeners\NetframeActionInsert',
        ],
        'App\Events\RemoveAction' => [
            'App\Listeners\NetframeActionDelete',
        ],
        'App\Events\InterestAction' => [
            'App\Listeners\InterestInsert',
        ],
        'App\Events\AddUserSkill' => [
            'App\Listeners\NotifyNewSkill',
        ],
        'App\Events\SocialAction' => [
            'App\Listeners\NotifySocialAction',
        ],
        'App\Events\AddProfile' => [
            'App\Listeners\ForceCheckRights',
        ],
        'App\Events\PostNotif' => [
            'App\Listeners\PostNotifEvent',
        ],
        'App\Events\RemoveNotif' => [
            'App\Listeners\RemoveNotifEvent',
        ],
        'App\Events\ChangeConfidentiality' => [
            'App\Listeners\ApplyProfileConfidentiality',
        ],
        'App\Events\SubscribeToProfile' => [
            'App\Listeners\AddSubscription',
        ],
        'App\Events\UserLogguedEvent' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
        'App\Events\UserUpdatedEvent' => [
            'App\Listeners\UserUpdatedListener',
        ],
        'App\Events\UserUpdateEvent' => [
            'App\Listeners\UserUpdateListener',
        ],
        'App\Events\PostChannel' => [
            'App\Listeners\NotifNewChanPost',
        ],
        'App\Events\CollabStep' => [
            'App\Listeners\BroadcastStep',
        ],
        'App\Events\CollabTelepointer' => [
            'App\Listeners\BroadcastTelepointer',
        ],
        'App\Events\AutoMember' => [
            'App\Listeners\AddAutoMember',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
