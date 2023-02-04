<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\UserAuthLogger;
use App\Events\UserLogguedEvent;
use App\Instance;

class LogSuccessfulLogin
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
     * @param  Login  $event
     * @return void
     */
    public function handle(UserLogguedEvent $event)
    {

        $user = $event->user;
        $instance = $event->instance;
        if ($user instanceof \App\User) {
            $logger = new UserAuthLogger();
            $logger->instances_id = $instance->id;
            $logger->users_id = $user->id;
            $logger->save();
        }

        // $instance = Instance::find(session('instanceId'));
        // if($instance && $instance->subscribeValid()==3){
        //     session(['errorPayment' => 1]);
        // }

        // $begin_date = $instance->begin_date;
        // if($begin_date >     date("Y-m-d H:i:s")){
        //     $days_left = date_diff(date_create(date("Y-m-d H:i:s")), date_create($begin_date))->d;
        //     // session(['days_left' => $days_left]);
        //     $notif = [10, 5, 3, 1];
        //     //Create a notification
        //     if(in_array($begin_date, $notif))
        //         ;
        // }
    }
}
