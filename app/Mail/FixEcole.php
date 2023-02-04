<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Instance;

class FixEcole extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user = null, Instance $instance = null, $clearPass)
    {
        $this->user = $user;
        $this->instance = $instance;
        $this->clearPass = $clearPass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $instance = $this->instance;
        $instanceLogo = $instance->getParameter('main_logo_2018', true);
        if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
            $logoParams = json_decode($instanceLogo->parameter_value, true);
            $instanceLogo = base_path().'/storage/uploads/instances/'.$instance->id.'/logos/'.$logoParams['filename'];
            $data['instanceLogo'] = $instanceLogo;
        }

        $data = [
            'instance' => $this->instance,
            'user' => $this->user,
            'password' => $this->clearPass
        ];

        return $this->view('emails.boarding.fix-ecole', $data)
            ->subject('Modification de votre accès à '.$this->instance->name);
    }
}
