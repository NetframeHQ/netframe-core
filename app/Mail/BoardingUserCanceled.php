<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Boarding;
use App\Instance;

class BoardingUserCanceled extends Mailable
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
        $instance = $this->boarding->instance;

        $this->boarding->emailKey = base64_encode(
            $this->boarding->created_at . '|' . $this->boarding->id . '|' . $this->boarding->email
        );
        $this->boarding->boardingUrl = $instance->getUrl()
            . '/boarding/key/' . $this->boarding->slug
            . '/' . $this->boarding->emailKey;

        $data['boarding'] = $this->boarding;
        $data['instance'] = $this->boarding->instance;
        $data['instanceAdmin'] = $instance->users()->wherePivot('roles_id', '1')->first();

        return $this->view('emails.cron.boarding-user-cancel', $data)
            ->subject(trans('email.cron.boardingUserCanceled.subject'));
    }
}
