<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class Reminders extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $instance = $this->user->instances()->first();
        $instanceUrl = $instance->getUrl();

        $data = [];
        $data['instanceUrl'] = $instanceUrl;
        $data['instance'] = $instance;
        $data['user'] = $this->user;
        //$data['user']->nbMessages = $this->user->nbMessages + $this->user->nbChanMessages;

        $instanceLogo = $instance->getParameter('main_logo_2018', true);
        if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
            $mainLogo = json_decode($instanceLogo->parameter_value, true);
            $mainLogoUrl = storage_path().'/uploads/instances/'.$instance->id.'/logos/'.$mainLogo['filename'];
            $data['instanceLogo'] = $mainLogoUrl;
        }

        return $this->view('emails.cron.notif-messages', $data)
            ->subject(trans('email.cron.notifMessages.subject').' / '.$instance->name);
    }
}
