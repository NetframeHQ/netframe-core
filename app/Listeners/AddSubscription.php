<?php

namespace App\Listeners;

use App\Events\SubscribeToProfile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Subscription;
use App\Events\NewAction;
use App\Events\SocialAction;
use App\User;
use App\Project;
use App\House;
use App\Community;
use App\Friends;

class AddSubscription
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InterestAction  $event
     * @return void
     */
    public function handle(SubscribeToProfile $event)
    {
        if ($event->profile_type == 'Project') {
            //check if user has been accepted in project to autorized private content
            $user = User::find($event->user_id);
            $checkUser = $user->project()->where('projects_id', '=', $event->profile_id)->get();

            $subscrib = new Subscription();

            $project = Project::findOrFail($event->profile_id);
            if ($project->confidentiality == 0) {
                if (count($checkUser) == 0) {
                    return false;
                } else {
                    $subscrib->confidentiality = 0;
                }
            } else {
                $subscrib->confidentiality = 0;
            }
            if (count($checkUser) == 0) {
                $subscrib->confidentiality = 1;
            }

            $userIdCreator = $project->users_id;

            $subscrib->users_id = $event->user_id;
            $subscrib->instances_id = session('instanceId');
            $subscrib->profile_id = $event->profile_id;
            $subscrib->profile_type = "App\\".$event->profile_type;
            $subscrib->save();
        } elseif ($event->profile_type == 'Community') {
            //check if user has been accepted in community to autorized private content
            $user = User::find($event->user_id);
            $checkUser = $user->community()->where('community_id', '=', $event->profile_id)->get();

            $subscrib = new Subscription();

            $community = Community::findOrFail($event->profile_id);
            if ($community->confidentiality == 0) {
                if (count($checkUser) == 0) {
                    return false;
                } else {
                    $subscrib->confidentiality = 0;
                }
            } else {
                $subscrib->confidentiality = 0;
            }
            if (count($checkUser) == 0) {
                $subscrib->confidentiality = 1;
            }

            $userIdCreator = $community->users_id;

            $subscrib->users_id = $event->user_id;
            $subscrib->instances_id = session('instanceId');
            $subscrib->profile_id = $event->profile_id;
            $subscrib->profile_type = "App\\".$event->profile_type;
            $subscrib->save();
        } elseif ($event->profile_type == 'House') {
            //check if user has been accepted in community to autorized private content
            $user = User::find($event->user_id);
            $checkUser = $user->house()->where('houses_id', '=', $event->profile_id)->get();

            $subscrib = new Subscription();

            $house = House::findOrFail($event->profile_id);
            if ($house->confidentiality == 0) {
                if (count($checkUser) == 0) {
                    return false;
                } else {
                    $subscrib->confidentiality = 0;
                }
            } else {
                $subscrib->confidentiality = 0;
            }
            if (count($checkUser) == 0) {
                $subscrib->confidentiality = 1;
            }

            $userIdCreator = $house->users_id;

            $subscrib->users_id = $event->user_id;
            $subscrib->instances_id = session('instanceId');
            $subscrib->profile_id = $event->profile_id;
            $subscrib->profile_type = "App\\".$event->profile_type;
            $subscrib->save();
        } elseif ($event->profile_type == 'Channel') {
            return;
        } else {
            // subscribe to personnal profiles
            $userSubscribe = User::find($event->profile_id);

            //check if both users are friends to define confidentiality
            $friend = new Friends();
            $is_friend = $friend->findByProfileId(0, 1, $event->profile_id)->count();
            $confidentiality = ($is_friend == 1) ? 0 : 1;

            $userIdCreator = $event->profile_id;

            // subscribe user profile
            $subscrib = new Subscription();
            $subscrib->users_id = $event->user_id;
            $subscrib->instances_id = session('instanceId');
            $subscrib->profile_id = $event->profile_id;
            // $subscrib->profile_type = "App\\".$event->profile_type;
            $subscrib->profile_type = get_class($userSubscribe);
            $subscrib->confidentiality = $confidentiality;
            $subscrib->save();
        }

        //insert notification
        $profile = $subscrib->profile;
        if (class_basename($profile) == 'User') {
            $profile->users_id = $profile->id;
        }

        if ($event->user_id != $userIdCreator) {
            event(new SocialAction($profile->users_id, $profile->id, get_class($profile), 'followProfile'));

            //add netframe action
            event(new NewAction('follow', $event->profile_id, $event->profile_type, $event->user_id, 'user'));
        }
    }
}
