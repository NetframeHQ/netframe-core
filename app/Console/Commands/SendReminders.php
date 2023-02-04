<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\Reminders;
use App\Instance;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mails to users with waiting notifications or messages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentDay = date('w');

        $instances = Instance::where('active', '=', 1)->get();

        foreach ($instances as $instance) {
            $users = $instance->users()->select(['users.*',
                'Notifs.countN as nbNotifs',
                'Msg.countM as nbMessages',
                'Chan.countC as nbChanMessages',
            ])
            ->leftJoin(
                \DB::raw('(select count(id) as countN, author_id from notifications'
                    . ' where `read` = 0 group by `author_id`) Notifs'),
                function ($join) {
                    $join->on('Notifs.author_id', '=', 'users.id');
                }
            )
            ->leftJoin(
                \DB::raw('(select count(id) as countM, receiver_id from messages_mail'
                    . ' where `read` = 0 and `receiver_type` = "App\\\\User" group by `receiver_id`) Msg'),
                function ($join) {
                    $join->on('Msg.receiver_id', '=', 'users.id');
                }
            )->leftJoin(
                \DB::raw('(select count(channels_id) as countC, users_id from channels_has_news_feeds'
                    . ' where `read` = 0 group by `users_id`) Chan'),
                function ($join) {
                    $join->on('Chan.users_id', '=', 'users.id');
                }
            )
            ->leftJoin('bounce_emails', 'bounce_emails.email', '=', 'users.email')
            ->leftJoin('user_notifications', 'user_notifications.users_id', '=', 'users.id')
            ->where('user_notifications.device', '=', 'mail')
            ->where('user_notifications.frequency', '=', $currentDay)
            ->whereNull('bounce_emails.email')
            ->where('users.active', '=', 1)
            ->get();

            foreach ($users as $user) {
                if ($user->nbNotifs != 0 || $user->nbMessages != 0 || $user->nbChanMessages != 0) {
                    \App::setLocale($user->lang);
                    Mail::to($user->email)->send(new Reminders($user));

                    // test if user has virtual users attached
                    $virtualUsers = $user->virtualUsers()->where('active', '=', 1)->get();
                    foreach ($virtualUsers as $virtualUser) {
                        Mail::to($virtualUser->email)->send(new Reminders($user));
                    }
                }
            }
        }
    }
}
