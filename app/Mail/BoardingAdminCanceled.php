<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Boarding;
use App\Instance;

class BoardingAdminCanceled extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Boarding $boarding)
    {
        $this->boarding = $boarding;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['boarding'] = $this->boarding;

        return $this->view('emails.cron.boarding-admin-cancel', $data)
            ->subject(trans('email.cron.boardingAdminCancel.subject'));
    }
}
