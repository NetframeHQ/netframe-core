<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Instance;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataEmail)
    {
        $this->dataEmail = $dataEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (session()->has('instanceId')) {
            $instance = Instance::find(session('instanceId'));

            $instanceLogo = $instance->getParameter('main_logo_2018', true);
            if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
                $logoParams = json_decode($instanceLogo->parameter_value, true);
                $instanceLogo = base_path() . '/storage/uploads/instances/'
                    . $instance->id . '/logos/' . $logoParams['filename'];
                $this->dataEmail['instanceLogo'] = $instanceLogo;
            }
        }

        return $this->view('emails.auth.reminder', $this->dataEmail)
            ->subject(trans('email.resetPassword.subject'));
    }
}
