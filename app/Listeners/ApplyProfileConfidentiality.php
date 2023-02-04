<?php

namespace App\Listeners;

use App\Events\ChangeConfidentiality;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Subscription;
use \Carbon\Carbon;

class ApplyProfileConfidentiality
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
     * @param  ChangeConfidentiality  $event
     * @return void
     */
    public function handle(ChangeConfidentiality $event)
    {
        $newConfidentialitySubscribe = ($event->confidentiality == 0) ? 2 : 0;

        //change subscriptions for non members of community
        if (class_basename($event->profile) == 'Community') {
            $nonMembers = Subscription::where('profile_type', '=', 'Community')
                ->where('profile_id', '=', $event->profile->id)
                ->leftJoin('community_has_users', function ($joinU) {
                    $joinU->on('community_has_users.users_id', '=', 'subscriptions.users_id')
                    ->on('community_has_users.community_id', '=', 'subscriptions.profile_id');
                })
                ->where(function ($where) use ($event) {
                    if ($event->confidentiality == 0) {
                        $where->whereNull('community_has_users.users_id');
                    }
                })
                ->getQuery()
                ->update([
                    'subscriptions.confidentiality' => $newConfidentialitySubscribe,
                    'subscriptions.updated_at' => Carbon::now()
                ]);
        }

        //change subscriptions for non members of house
        if (class_basename($event->profile) == 'House') {
            $nonMembers = Subscription::where('profile_type', '=', 'House')
                ->where('profile_id', '=', $event->profile->id)
                ->leftJoin('houses_has_users', function ($joinU) {
                    $joinU->on('houses_has_users.users_id', '=', 'subscriptions.users_id')
                    ->on('houses_has_users.houses_id', '=', 'subscriptions.profile_id');
                })
                ->where(function ($where) use ($event) {
                    if ($event->confidentiality == 0) {
                        $where->whereNull('houses_has_users.users_id');
                    }
                })
                ->getQuery()
                ->update([
                    'subscriptions.confidentiality' => $newConfidentialitySubscribe,
                    'subscriptions.updated_at' => Carbon::now()
                ]);
        }

        //change subscriptions for non members of project
        if (class_basename($event->profile) == 'Project') {
            $nonMembers = Subscription::where('profile_type', '=', 'Project')
                ->where('profile_id', '=', $event->profile->id)
                ->leftJoin('projects_has_users', function ($joinU) {
                    $joinU->on('projects_has_users.users_id', '=', 'subscriptions.users_id')
                    ->on('projects_has_users.projects_id', '=', 'subscriptions.profile_id');
                })
                ->where(function ($where) use ($event) {
                    if ($event->confidentiality == 0) {
                        $where->whereNull('projects_has_users.users_id');
                    }
                })
                ->getQuery()
                ->update([
                    'subscriptions.confidentiality' => $newConfidentialitySubscribe,
                    'subscriptions.updated_at' => Carbon::now()
                ]);
        }

        // update newsfeed for netframe action confidentiality
        $nfActions = $event->profile->netframeActions;
        if ($event->confidentiality == 0) { // private
            $newConfidentialityNf = 0;
            $newPrivatePRofileNf = 1;
        } else { // public
            $newConfidentialityNf = 1;
            $newPrivatePRofileNf = 0;
        }
        foreach ($nfActions as $nfa) {
            $newsFeed = $nfa->newsfeedRef;
            if ($newsFeed != null) {
                $newsFeed->confidentiality = $newConfidentialityNf;
                $newsFeed->private_profile = $newPrivatePRofileNf;
                $newsFeed->save();
            }
        }
    }
}
