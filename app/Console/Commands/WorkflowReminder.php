<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\User;
use Carbon\Carbon;
use App\Instance;
use App\WorkflowDetailsAction;
use App\Mail\WorkflowReminder as WfRemind;

class WorkflowReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mails to users having workflow not treated with end date';

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
        // get all action on active instances with end date which are not treated
        $actions = WorkflowDetailsAction::leftJoin(
            'instances',
            'instances.id',
            '=',
            'workflow_details_actions.instances_id'
        )
        ->where('instances.active', '=', 1)
        ->leftJoin(
            'users',
            'users.id',
            '=',
            'workflow_details_actions.users_id'
        )
        ->where('users.active', '=', 1)
        ->whereNotNull('workflow_details_actions.action_date')
        ->where(\DB::raw('date(workflow_details_actions.action_date)'), '=', Carbon::now()->addDays(5)->format('Y-m-d'))
        ->whereNull('workflow_details_actions.action_result')
        ->get();

        foreach ($actions as $action) {
            // send email to user
            $workflow = $action->workflow;
            $user = $action->user;
            \App::setLocale($user->lang);
            Mail::to($user->email)->send(new WfRemind($user, $workflow, $action));
        }
    }
}
