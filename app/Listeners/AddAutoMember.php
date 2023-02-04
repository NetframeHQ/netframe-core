<?php

namespace App\Listeners;

use App\Events\AutoMember;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Project;
use App\House;
use App\Community;
use App\Instance;
use App\User;
use App\Subscription;

class AddAutoMember
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
     * @param  AutoMmeber  $event
     * @return void
     */
    public function handle(AutoMember $event)
    {
        $roleId = $event->role;

        // get instance users
        $instance = Instance::find(session('instanceId'));
        $users = $instance->users()->pluck('users.id')->toArray();

        /*
         *
         * FOR MEMBERSHIP
         *
         */

        // detach inactive members
        $inactiveMembers = $event->profile->users()->wherePivot('status', '=', 0)->pluck('users.id')->toArray();
        $event->profile->users()->detach($inactiveMembers);

        // get all users atatched to profile
        $members = $event->profile->allUsers()->pluck('users.id')->toArray();

        $usersToAdd = array_diff($users, $members);

        $usersToAddRole = [];
        foreach ($usersToAdd as $userId) {
            $usersToAddRole[$userId] = [
                'status' => '1',
                'roles_id' => $roleId,
            ];
        }
        // attach users to profile
        $event->profile->users()->attach($usersToAddRole);


        /*
         *
         * FOR SUBSCRIPTION
         *
         */
        $subscribed = $instance->users()->leftJoin('subscriptions', 'subscriptions.users_id', '=', 'users.id')
            ->where('profile_type', '=', get_class($event->profile))
            ->where('profile_id', '=', $event->profile->id)
            ->pluck('users.id')->toArray();
        $diffUsers = array_diff($users, $subscribed);
        // insert diff unsers in subscription
        $arrayNewSubscribe = [];
        foreach ($diffUsers as $userId) {
            $arrayNewSubscribe[] = [
                'users_id' => $userId,
                'instances_id' => $instance->id,
                'profile_id' => $event->profile->id,
                'profile_type' => get_class($event->profile),
                'level' => 1,
                'confidentiality' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateString(),
                'updated_at' => \Carbon\Carbon::now()->toDateString(),
            ];
        }
        Subscription::insert($arrayNewSubscribe);

        // force check rights of users
        User::whereIn('id', $usersToAdd)->update(['check_rights' => 1]);
    }
}
