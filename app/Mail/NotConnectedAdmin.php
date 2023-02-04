<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class NotConnectedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $nbDays)
    {
        $this->user = $user;
        $this->nbDays = $nbDays;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [];
        $data['nbWeek'] = ($this->nbDays < 10 ) ? 1 : 2 ;
        $data['user'] = $this->user;
        //invitation mail
        $instance = $this->user->instances()->first();
        $instanceLogo = $instance->getParameter('main_logo_2018', true);
        if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
            $logoParams = json_decode($instanceLogo->parameter_value, true);
            $instanceLogo = base_path().'/storage/uploads/instances/'.$instance->id.'/logos/'.$logoParams['filename'];
            $data['instanceLogo'] = $instanceLogo;
        }
        $data['instance'] = $instance;

        return $this->view('emails.cron.not-connected-admin', $data)
            ->subject(trans('email.cron.notConnectedAdmin.subject'));
    }
}
