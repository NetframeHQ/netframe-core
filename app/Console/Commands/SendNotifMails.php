<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BoardingAdminCanceled;
use App\Mail\BoardingUserCanceled;
use App\Mail\WelcomeAdmin;
use App\Mail\NotConnected;
use App\Mail\NotConnectedAdmin;
use App\User;
use App\Boarding;
use Carbon\Carbon;
use App\Instance;

class SendNotifMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifmails {type} {--frequency=null}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mails to users depending of their interactions wtih netframe';

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
        $instances = Instance::where('active', '=', 1)->get();

        switch ($this->argument('type')) {
            case "unboardedAdmin":
                // h+1
                if ($this->option('frequency') == 'hour') {
                    $boardings = Boarding::whereNotNull('boarding_key')
                        ->where('send_notif', '=', 0)
                        ->where('updated_at', '<', Carbon::now()->subHours(1))
                        ->get();
                }

                //j+1
                if ($this->option('frequency') == 'day') {
                    $boardings = Boarding::whereNotNull('boarding_key')
                        ->where('send_notif', '=', 1)
                        ->where('updated_at', '<', Carbon::now()->subDays(1))
                        ->get();
                }

                // send mail and change send_notif tag
                foreach ($boardings as $boarding) {
                    $boarding->send_notif = $boarding->send_notif+1;
                    $boarding->save();
                    \App::setLocale($boarding->lang);
                    Mail::to($boarding->email)->send(new BoardingAdminCanceled($boarding));
                }

                break;
            case "welcomeAdmin": // include user manual
                if ($this->option('frequency') == 'hour') {
                    $mailType = "welcome";
                    $newInstances = Instance::where('created_at', '>', Carbon::now()->subHours(2))
                        ->where('created_at', '<=', Carbon::now()->subHours(1))
                        ->get();
                }

                if ($this->option('frequency') == 'day') {
                    $mailType = "userManual";
                    $newInstances = Instance::whereDate(
                        'created_at',
                        '=',
                        Carbon::now()->subDays(1)->toDateString()
                    )->get();
                }

                // send mail to owner
                foreach ($newInstances as $instance) {
                    $admin = $instance->users()->wherePivot('roles_id', '1')->first();
                    if ($admin != null) {
                        \App::setLocale($admin->lang);
                        if ($this->option('frequency') == 'day' && $admin->lang != 'fr') {
                            continue;
                        }
                        Mail::to($admin->email)->send(new WelcomeAdmin($admin, $mailType));
                    }
                }
                break;
            case "unboardedUser":
                // h+1
                if ($this->option('frequency') == 'hour') {
                    $boardings = Boarding::select('boarding.*')
                        ->whereNotNull('boarding.slug')
                        ->leftJoin('instances', 'instances.id', '=', 'boarding.instances_id')
                        ->where('instances.active', '=', 1)
                        ->where('boarding.send_notif', '=', 0)
                        ->where('boarding.updated_at', '<', Carbon::now()->subHours(1))
                        ->get();
                }

                //j+1
                if ($this->option('frequency') == 'day') {
                    $boardings = Boarding::select('boarding.*')
                        ->whereNotNull('boarding.slug')
                        ->leftJoin('instances', 'instances.id', '=', 'boarding.instances_id')
                        ->where('instances.active', '=', 1)
                        ->where('boarding.send_notif', '=', 1)
                        ->where('boarding.updated_at', '<', Carbon::now()->subDays(1))
                        ->get();
                }

                //j+7
                if ($this->option('frequency') == 'week') {
                    $boardings = Boarding::select('boarding.*')
                        ->whereNotNull('boarding.slug')
                        ->leftJoin('instances', 'instances.id', '=', 'boarding.instances_id')
                        ->where('instances.active', '=', 1)
                        ->where('boarding.send_notif', '=', 2)
                        ->where('boarding.updated_at', '<', Carbon::now()->subDays(7))
                        ->get();
                }

                // send mail and change send_notif tag
                foreach ($boardings as $boarding) {
                    \App::setLocale($boarding->lang);
                    $boarding->send_notif = $boarding->send_notif+1;
                    $boarding->save();
                    Mail::to($boarding->email)->send(new BoardingUserCanceled($boarding));
                }
                break;

            case "unconnectedAdmin":
                if (in_array($this->option('frequency'), [7, 14])) {
                    $newInstances = Instance::whereDate(
                        'created_at',
                        '=',
                        Carbon::now()->subDays($this->option('frequency'))->toDateString()
                    )->get();
                    foreach ($newInstances as $instance) {
                        $admins = $instance
                            ->users()
                            ->wherePivot('roles_id', '1')
                            ->whereDate(
                                'users.updated_at',
                                '<=',
                                Carbon::now()->subDays($this->option('frequency')-1)->toDateString()
                            )
                            ->get();
                        foreach ($admins as $admin) {
                            \App::setLocale($admin->lang);
                            Mail::to($admin->email)->send(new NotConnectedAdmin($admin, $this->option('frequency')));
                        }
                    }
                }
                break;

            case "unconnectedUser":
                if (in_array($this->option('frequency'), [7, 14, 30])) {
                    foreach ($instances as $instance) {
                        $users = $instance
                            ->users()
                            ->select('users.*')
                            ->where('users.active', '=', 1)
                            ->whereDate(
                                'users.updated_at',
                                '=',
                                Carbon::now()->subDays($this->option('frequency'))->toDateString()
                            )
                            ->get();
                        foreach ($users as $user) {
                            \App::setLocale($user->lang);
                            Mail::to($user->email)->send(new NotConnected($user, $this->option('frequency')));
                        }
                    }
                }
                break;
        }
    }
}
