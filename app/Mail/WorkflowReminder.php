<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WorkflowReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $workflow, $action)
    {
        $this->user = $user;
        $this->wf = $workflow;
        $this->wfAction = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $instance = $this->user->instances()->first();
        $instanceLogo = $instance->getParameter('main_logo_2018', true);
        if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
            $logoParams = json_decode($instanceLogo->parameter_value, true);
            $instanceLogo = base_path().'/storage/uploads/instances/'.$instance->id.'/logos/'.$logoParams['filename'];
            $data['instanceLogo'] = $instanceLogo;
        }

        $data = [
            'instance' => $instance,
            'user' => $this->user,
            'workflow' => $this->wf,
            'wfAction' => $this->wfAction,
        ];

        return $this->view('emails.cron.workflow-action', $data)
            ->subject(trans('email.cron.notConnectedUser.subject'));
    }
}
