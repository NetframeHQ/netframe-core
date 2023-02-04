<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Boarding;
use App\Instance;

class BoardingDemand extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Boarding $boarding = null, Instance $instance = null)
    {
        $this->boarding = $boarding;
        $this->instance = $instance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->instance != null) {
            //instance open confirmation mail
            return $this->view('emails.boarding.instance', ['instance' => $this->instance])
            ->subject(trans('boarding.emailContent.instance.title'));
        } elseif (isset($this->boarding->userFrom) && $this->boarding->userFrom != null) {
            $data = [];
            $data['boarding'] = $this->boarding;
            //invitation mail
            $instance = $this->boarding->instance;
            $instanceLogo = $instance->getParameter('main_logo_2018', true);
            if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
                $logoParams = json_decode($instanceLogo->parameter_value, true);
                $instanceLogo = base_path()
                    . '/storage/uploads/instances/' . $instance->id
                    . '/logos/' . $logoParams['filename'];
                $data['instanceLogo'] = $instanceLogo;
            }
            return $this->view('emails.boarding.invite', $data)
                ->subject($this->boarding->userFrom->getNameDisplay().' '.trans('boarding.emailContent.invite.title'));
        } else {
            //first boarding, open instance
            return $this->view('emails.boarding.demand', ['boarding' => $this->boarding])
                ->subject(trans('boarding.emailContent.demand.title'));
        }
    }
}
